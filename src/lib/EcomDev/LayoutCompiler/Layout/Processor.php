<?php

use EcomDev_LayoutCompiler_Contract_Layout_ItemInterface as ItemInterface;

class EcomDev_LayoutCompiler_Layout_Processor 
    implements EcomDev_LayoutCompiler_Contract_Layout_ProcessorInterface
{
    use EcomDev_LayoutCompiler_LayoutAwareTrait;

    /**
     * List of all registered items
     *
     * @var ItemInterface[]
     */
    private $items = array();

    /**
     * List of items by execution type
     *
     * @var ItemInterface[][]
     */
    private $itemByType = array();

    /**
     * List of items associated with block identifier
     *
     * @var ItemInterface[][]
     */
    private $itemByBlock = array();

    /**
     * List of items associated with block and particular execution type
     *
     * @var ItemInterface[][][]
     */
    private $itemByBlockAndType = array();
    
    
    /**
     * Adds an item to layout
     *
     * @param ItemInterface $item
     * @return $this
     */
    public function addItem(ItemInterface $item)
    {
        $itemType = $item->getType();
        
        if ($itemType === ItemInterface::TYPE_INITIALIZE) {
            // If item is an initialize type object, just directly execute it
            $item->execute($this->getLayout(), $this);
            return $this;
        }
        
        $itemId = $this->getItemId($item);
        $this->items[$itemId] = $item;
        $this->itemByType[$itemType][$itemId] = $item;
        if ($item instanceof EcomDev_LayoutCompiler_Contract_Layout_BlockAwareInterface) {
            foreach ($item->getPossibleBlockIdentifiers() as $blockId) {
                $this->itemByBlock[$blockId][$itemId] = $item;
                $this->itemByBlockAndType[$blockId][$itemType][$itemId] = $item;
            }
        }
        
        return $this;
    }

    /**
     * Returns identifier of the item
     * 
     * @return string
     */
    private function getItemId(ItemInterface $item)
    {
        return spl_object_hash($item);
    }

    /**
     * Removes an item from a layout
     *
     * @param ItemInterface $item
     * @return $this
     */
    public function removeItem(ItemInterface $item)
    {
        $itemId = $this->getItemId($item);
        $itemType = $item->getType();
        
        unset($this->itemByType[$itemType][$itemId]);
        unset($this->items[$itemId]);
        
        if ($item instanceof EcomDev_LayoutCompiler_Contract_Layout_BlockAwareInterface) {
            foreach ($item->getPossibleBlockIdentifiers() as $block) {
                unset($this->itemByBlock[$block][$itemId]);
                unset($this->itemByBlockAndType[$block][$itemType][$itemId]);
            }
        }
        
        return $this;
    }

    /**
     * Returns item list by block identifier
     *
     * @param string $identifier
     * @return ItemInterface[]
     */
    public function findItemsByBlockId($identifier)
    {
        $result = array();
        
        if (isset($this->itemByBlock[$identifier])) {
            $result = array_values($this->itemByBlock[$identifier]);
        }

        return $result;
    }

    /**
     * Returns item list by type of operation
     *
     * @param int $type
     * @return ItemInterface[]
     */
    public function findItemsByType($type)
    {
        $result = array();
        
        if (isset($this->itemByType[$type])) {
            $result = array_values($this->itemByType[$type]);
        }
        
        return $result;
    }

    /**
     * Returns item list by block identifier and type of load
     *
     * @param string $identifier
     * @param int $type
     * @return ItemInterface[]
     */
    public function findItemsByBlockIdAndType($identifier, $type)
    {
        $result = array();
        
        if (isset($this->itemByBlockAndType[$identifier][$type])) {
            $result = array_values($this->itemByBlockAndType[$identifier][$type]);
        }
        
        return $result;
    }

    /**
     * Execute a type of action depending on a layout instance
     *
     * @param int $type
     * @return $this
     */
    public function execute($type)
    {
        foreach ($this->findItemsByType($type) as $item) {
            $item->execute($this->getLayout(), $this);
        }
        
        return $this;
    }

    /**
     * Resets items to be executed
     *
     * @return $this
     */
    public function reset()
    {
        $this->items = array();
        $this->itemByType = array();
        $this->itemByBlock = array();
        $this->itemByBlockAndType = array();
        return $this;
    }
}
