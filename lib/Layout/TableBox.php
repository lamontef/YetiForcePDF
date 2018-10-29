<?php
declare(strict_types=1);
/**
 * TableBox class
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
 * Class TableBox
 */
class TableBox extends BlockBox
{
    /**
     * Append table row group box element
     * @param \DOMNode $childDomElement
     * @param Element $element
     * @param Style $style
     * @param \YetiForcePDF\Layout\BlockBox $parentBlock
     * @return $this
     */
    public function appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock)
    {
        $box = (new TableRowGroupBox())
            ->setDocument($this->document)
            ->setParent($this)
            ->setElement($element)
            ->setStyle($style)
            ->init();
        $this->appendChild($box);
        $box->getStyle()->init();
        // we don't want to build tree from here - we will do it in TableRowBox
        return $box;
    }

    /**
     * Get all rows from all row groups
     */
    public function getRows()
    {
        $rows = [];
        foreach ($this->getChildren() as $rowGroup) {
            foreach ($rowGroup->getChildren() as $row) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Get columns - get table cells segregated by columns
     * @return array
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->getRows() as $row) {
            foreach ($row->getChildren() as $columnIndex => $column) {
                $columns[$columnIndex][] = $column;
            }
        }
        return $columns;
    }

    /**
     * Fill missing cells - rows should have equal numbers of column so if not we will add anonymous cell to it
     * @return $this
     */
    public function fillMissingCells()
    {

        return $this;
    }


    /**
     * Measure width of this block
     * @return $this
     */
    public function measureWidth()
    {
        parent::measureWidth();
        $maxWidths = [];
        $columns = $this->getColumns();
        foreach ($columns as $columnIndex => $row) {
            foreach ($row as $column) {
                $cell = $column->getFirstChild();
                if (!isset($maxWidths[$columnIndex])) {
                    $maxWidths[$columnIndex] = 0;
                }
                $maxWidths[$columnIndex] = max($maxWidths[$columnIndex], $cell->getDimensions()->getWidth());
            }
        }
        $maxWidth = 0;
        foreach ($maxWidths as $width) {
            $maxWidth += $width;
        }
        $this->getDimensions()->setWidth($maxWidth);
        foreach ($maxWidths as $columnIndex => $width) {
            foreach ($columns[$columnIndex] as $row) {
                $cell = $row->getFirstChild();
                $row->getDimensions()->setWidth($width);
                $cell->getDimensions()->setWidth($width);
                foreach ($cell->getChildren() as $child) {
                    $child->measureWidth();
                }
            }
        }
        return $this;
    }
}