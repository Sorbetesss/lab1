<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\AstGenerator\Hydrate;

use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use Symfony\Component\AstGenerator\AstGeneratorInterface;
use Symfony\Component\AstGenerator\Exception\MissingContextException;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * Abstract class to generate hydration of data from object
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
abstract class HydrateFromObjectGenerator implements AstGeneratorInterface
{
    /** @var PropertyInfoExtractorInterface Extract list of properties from a class */
    protected $propertyInfoExtractor;

    /** @var AstGeneratorInterface Generator for hydration of types */
    protected $typeHydrateAstGenerator;

    public function __construct(PropertyInfoExtractorInterface $propertyInfoExtractor, AstGeneratorInterface $typeHydrateAstGenerator)
    {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->typeHydrateAstGenerator = $typeHydrateAstGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = array())
    {
        if (!isset($context['input']) || !($context['input'] instanceof Expr\Variable)) {
            throw new MissingContextException('Input variable not defined or not a Expr\Variable in generation context');
        }

        if (!isset($context['output']) || !($context['output'] instanceof Expr\Variable)) {
            throw new MissingContextException('Output variable not defined or not a Expr\Variable in generation context');
        }

        $statements = array($this->getAssignStatement($context['output']));

        foreach ($this->propertyInfoExtractor->getProperties($object, $context) as $property) {
            // Only normalize readable property
            if (!$this->propertyInfoExtractor->isReadable($object, $property, $context)) {
                continue;
            }

            // @TODO Have property info extractor extract the way of reading a property (public or method with method name)
            $input = new Expr\MethodCall($context['input'], 'get'.ucfirst($property));
            $output = $this->getSubAssignVariableStatement($context['output'], $property);
            $types = $this->propertyInfoExtractor->getTypes($object, $property, $context);

            // If no type can be extracted, directly assign output to input
            if (null === $types || count($types) == 0) {
                $statements[] = new Expr\Assign($output, $input);

                continue;
            }

            // If there is multiple types, we need to know which one we must normalize
            $conditionNeeded = (boolean) (count($types) > 1);
            $noAssignment = true;

            foreach ($types as $type) {
                if (!$this->typeHydrateAstGenerator->supportsGeneration($type)) {
                    continue;
                }

                $noAssignment = false;
                $statements = array_merge($statements, $this->typeHydrateAstGenerator->generate($type, array_merge($context, [
                    'input' => $input,
                    'output' => $output,
                    'condition' => $conditionNeeded,
                ])));
            }

            // If nothing has been assigned, we directly put input into output
            if ($noAssignment) {
                $statements[] = new Expr\Assign($output, $input);
            }
        }

        return $statements;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsGeneration($object)
    {
        return is_string($object) && class_exists($object);
    }

    /**
     * Create the assign statement.
     *
     * @param Expr\Variable $dataVariable Variable to use
     *
     * @return Expr\Assign An assignment for the variable
     */
    abstract protected function getAssignStatement($dataVariable);

    /**
     * Create the sub assign variable statement.
     *
     * @param Expr\Variable $dataVariable Variable to use
     * @param string        $property     Property name for object or array dimension
     *
     * @return Expr\ArrayDimFetch|Expr\PropertyFetch
     */
    abstract protected function getSubAssignVariableStatement($dataVariable, $property);
}
