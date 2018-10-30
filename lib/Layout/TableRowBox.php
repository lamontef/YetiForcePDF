<?php
declare(strict_types=1);
/**
 * TableRowBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Style\Style;
use \YetiForcePDF\Html\Element;
use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;

/**
 * Class TableRowBox
 */
class TableRowBox extends BlockBox
{

    /**
     * We shouldn't append block box here
     */
    public function appendBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append table wrapper here
     */
    public function appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append inline block box here
     */
    public function appendInlineBlockBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * We shouldn't append inline box here
     */
    public function appendInlineBox($childDomElement, $element, $style, $parentBlock)
    {
    }

    /**
     * Append table cell box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableCellBox($childDomElement, $element, $style, $parentBlock)
    {
        $clearStyle = (new \YetiForcePDF\Style\Style())
            ->setDocument($this->document)
            ->parseInline();
        $column = (new TableColumnBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setStyle($clearStyle)
            ->init();
        $this->appendChild($column);
        $column->getStyle()->init();
        $box = (new TableCellBox())
            ->setDocument($this->document)
            ->setParent($column)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $column->appendChild($box);
        $box->getStyle()->init();
        $box->buildTree($box);
        return $box;
    }

    /**
     * Measure width of this block
     * @return $this
     */
    public function measureWidth()
    {
        $dimensions = $this->getDimensions();
        $parent = $this->getParent();
        if ($parent) {
            if ($parent->getDimensions()->getWidth() !== null) {
                $dimensions->setWidth($parent->getDimensions()->getInnerWidth() - $this->getStyle()->getHorizontalMarginsWidth());
                $this->applyStyleWidth();
                foreach ($this->getChildren() as $child) {
                    $child->measureWidth();
                }
                return $this;
            }
            foreach ($this->getChildren() as $child) {
                $child->measureWidth();
            }
            $maxWidth = '0';
            foreach ($this->getChildren() as $child) {
                $maxWidth = bcadd($maxWidth, (string)$child->getDimensions()->getOuterWidth(), 4);
            }
            $style = $this->getStyle();
            $maxWidth = bcadd($maxWidth, bcadd((string)$style->getHorizontalBordersWidth(), (string)$style->getHorizontalPaddingsWidth(), 4), 4);
            $maxWidth = bcsub($maxWidth, (string)$style->getHorizontalMarginsWidth());
            $maxWidth = (float)$maxWidth;
            $dimensions->setWidth($maxWidth);
            $this->applyStyleWidth();
            return $this;
        }
        $dimensions->setWidth($this->document->getCurrentPage()->getDimensions()->getWidth());
        $this->applyStyleWidth();
        foreach ($this->getChildren() as $child) {
            $child->measureWidth();
        }
        return $this;
    }

    /**
     * Measure height
     * @return $this
     */
    public function measureHeight()
    {
        foreach ($this->getChildren() as $child) {
            $child->measureHeight();
        }
        $height = 0;
        foreach ($this->getChildren() as $child) {
            $height = bccomp((string)$height, (string)$child->getDimensions()->getOuterHeight(),4) >0 ? $height : $child->getDimensions()->getOuterHeight();
        }
        $rules = $this->getStyle()->getRules();
        $height = bcadd((string)$height, bcadd((string)$rules['border-top-width'], (string)$rules['padding-top'], 4), 4);
        $height = bcadd($height, bcadd((string)$rules['border-bottom-width'], (string)$rules['padding-bottom'], 4), 4);
        $this->getDimensions()->setHeight((float)$height);
        $this->applyStyleHeight();
        return $this;
    }

    /**
     * Offset elements
     * @return $this
     */
    public function measureOffset()
    {
        $top = $this->document->getCurrentPage()->getCoordinates()->getY();
        $left = $this->document->getCurrentPage()->getCoordinates()->getX();
        $marginTop = $this->getStyle()->getRules('margin-top');
        if ($parent = $this->getParent()) {
            $parentStyle = $parent->getStyle();
            $top = $parentStyle->getOffsetTop();
            $left = $parentStyle->getOffsetLeft();
            if ($previous = $this->getPrevious()) {
                $top = bcadd((string)$previous->getOffset()->getTop(), (string)$previous->getDimensions()->getHeight(), 4);
                if ($previous->getStyle()->getRules('display') === 'block') {
                    $marginTop = bccomp((string)$marginTop, (string)$previous->getStyle()->getRules('margin-bottom'),4) >0 ? $marginTop : $previous->getStyle()->getRules('margin-bottom');
                } elseif (!$previous instanceof LineBox) {
                    $marginTop = (float)bcadd((string)$marginTop, (string)$previous->getStyle()->getRules('margin-bottom'), 4);
                }
            }
        }
        $top = (float)bcadd((string)$top, (string)$marginTop, 4);
        $left = (float)bcadd((string)$left, (string)$this->getStyle()->getRules('margin-left'), 4);
        $this->getOffset()->setTop($top);
        $this->getOffset()->setLeft($left);
        foreach ($this->getChildren() as $child) {
            $child->measureOffset();
        }
        return $this;
    }

    /**
     * Position
     * @return $this
     */
    public function measurePosition()
    {
        $x = $this->document->getCurrentPage()->getCoordinates()->getX();
        $y = $this->document->getCurrentPage()->getCoordinates()->getY();
        if ($parent = $this->getParent()) {
            $x = (float)bcadd((string)$parent->getCoordinates()->getX(), (string)$this->getOffset()->getLeft(), 4);
            $y = (float)bcadd((string)$parent->getCoordinates()->getY(), (string)$this->getOffset()->getTop(), 4);
        }
        $this->getCoordinates()->setX($x);
        $this->getCoordinates()->setY($y);
        foreach ($this->getChildren() as $child) {
            $child->measurePosition();
        }
        return $this;
    }
}
