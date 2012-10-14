<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * The Dialog class provides helpers to interact with the user.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DialogHelper extends Helper
{
    private $inputStream;

    /**
     * Asks a question to the user.
     *
     * @param OutputInterface $output       An Output instance
     * @param string|array    $question     The question to ask
     * @param string          $default      The default answer if none is given by the user
     * @param array           $autocomplete List of values to autocomplete
     *
     * @return string The user answer
     *
     * @throws \RuntimeException If there is no data to read in the input stream
     */
    public function ask(OutputInterface $output, $question, $default = null, $autocomplete = null)
    {
        $output->write($question);

        $inputStream = (null === $this->inputStream ? STDIN : $this->inputStream);

        if (null === $autocomplete || !($output instanceof StreamOutput && $output->hasColorSupport())) {
            $ret = fgets($inputStream);
            if (false === $ret) {
                throw new \RuntimeException('Aborted');
            }
            $ret = trim($ret);
        } else {
            $i = 0;
            $currentMatched = false;
            $ret = '';

            // Disable icanon (so we can fread each keypress) and echo (we'll do echoing here instead)
            system("stty -icanon -echo");

            while ($c = fread($inputStream, 3)) {
                // Did we read an escape character?
                if (strlen($c) > 1 && $c[0] == "\033") {
                    // Escape sequences for arrow keys
                    if ($c[2] == 'A' || $c[2] == 'B' || $c[2] == 'C' || $c[2] == 'D') {
                        continue;
                    }
                }

                // Backspace Character
                if (ord($c) === 127) {
                    if ($i === 0) {
                        continue;
                    }

                    // Move cursor backwards
                    $output->write("\033[1D");
                    // Erase characters from cursor to end of line
                    $output->write("\033[K");
                    $ret = substr($ret, 0, --$i);

                    continue;
                }

                if ($c == "\t" | $c == "\n") {
                    if (false !== $currentMatched) {
                        // Echo out completed match
                        $output->write(substr($autocomplete[$currentMatched], strlen($ret)));
                        $ret = $autocomplete[$currentMatched];
                        $i = strlen($ret);
                    }

                    if ($c == "\n") {
                        $output->write($c);
                        break;
                    }

                    continue;
                }

                $output->write($c);
                $ret .= $c;
                $i++;

                // Erase characters from cursor to end of line
                $output->write("\033[K");

                for ($j = 0; $j < count($autocomplete); $j++) {
                    $matchTest = substr($autocomplete[$j], 0, strlen($ret));

                    if ($ret == $matchTest) {
                        // Save cursor position
                        $output->write("\0337");

                        // Set fore/background colour to make text appear highlighted
                        $output->write("\033[47;30m");
                        $output->write(substr($autocomplete[$j], strlen($ret)));
                        // Reset text colour
                        $output->write("\033[0m");

                        // Restore cursor position
                        $output->write("\0338");

                        $currentMatched = $j;
                        break;
                    }

                    $currentMatched = false;
                }
            }

            // Reset stty so it behaves normally again
            system("stty icanon echo");
        }

        return strlen($ret) > 0 ? $ret : $default;
    }

    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answers by nothing, yes, or no.
     *
     * @param OutputInterface $output   An Output instance
     * @param string|array    $question The question to ask
     * @param Boolean         $default  The default answer if the user enters nothing
     *
     * @return Boolean true if the user has confirmed, false otherwise
     */
    public function askConfirmation(OutputInterface $output, $question, $default = true)
    {
        $answer = 'z';
        while ($answer && !in_array(strtolower($answer[0]), array('y', 'n'))) {
            $answer = $this->ask($output, $question);
        }

        if (false === $default) {
            return $answer && 'y' == strtolower($answer[0]);
        }

        return !$answer || 'y' == strtolower($answer[0]);
    }

    /**
     * Asks for a value and validates the response.
     *
     * The validator receives the data to validate. It must return the
     * validated data when the data is valid and throw an exception
     * otherwise.
     *
     * @param OutputInterface $output       An Output instance
     * @param string|array    $question     The question to ask
     * @param callback        $validator    A PHP callback
     * @param integer         $attempts     Max number of times to ask before giving up (false by default, which means infinite)
     * @param string          $default      The default answer if none is given by the user
     * @param array           $autocomplete List of values to autocomplete
     *
     * @return mixed
     *
     * @throws \Exception When any of the validators return an error
     */
    public function askAndValidate(OutputInterface $output, $question, $validator, $attempts = false, $default = null, $autocomplete = null)
    {
        $error = null;
        while (false === $attempts || $attempts--) {
            if (null !== $error) {
                $output->writeln($this->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
            }

            $value = $this->ask($output, $question, $default, $autocomplete);

            try {
                return call_user_func($validator, $value);
            } catch (\Exception $error) {
            }
        }

        throw $error;
    }

    /**
     * Sets the input stream to read from when interacting with the user.
     *
     * This is mainly useful for testing purpose.
     *
     * @param resource $stream The input stream
     */
    public function setInputStream($stream)
    {
        $this->inputStream = $stream;
    }

    /**
     * Returns the helper's input stream
     *
     * @return string
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'dialog';
    }
}
