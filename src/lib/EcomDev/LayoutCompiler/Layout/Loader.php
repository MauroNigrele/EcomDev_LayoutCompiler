<?php

use EcomDev_LayoutCompiler_Contract_Layout_ProcessorInterface as ProcessorInterface;
use EcomDev_LayoutCompiler_Contract_Layout_ItemInterface as ItemInterface;
use EcomDev_LayoutCompiler_Contract_IndexInterface as IndexInterface;

class EcomDev_LayoutCompiler_Layout_Loader
    implements EcomDev_LayoutCompiler_Contract_Layout_LoaderInterface
{
    /**
     * Loaded handles hash of flags
     * 
     * @var array
     */
    private $loaded = array();
    
    /**
     * Loads handle items
     *
     * @param string|string[] $handleName
     * @param IndexInterface $index
     * @return ItemInterface[]
     */
    public function load($handleName, IndexInterface $index)
    {
        $items = array();
        $total = 0;
        $files = $index->getHandleIncludes($handleName);
        
        $includeFile = function ($file) {
            $value = @include $file;
            if (!is_array($value)) {
                $value = array();
            }
            return $value;
        };
            
        foreach ($files as $file) {
            $newItems = $includeFile($file);
            array_splice($items, $total, 0, $newItems);
            $total += count($newItems);
        }
        
        $this->loaded[$handleName] = true;
        return $items;
    }

    /**
     * Check if handle has been already loaded
     *
     * @param string $handle
     * @return bool
     */
    public function isLoaded($handle)
    {
        return isset($this->loaded[$handle]);
    }

    /**
     * Loads a handles into layout processor
     *
     * @param string|string[] $handleName
     * @param ProcessorInterface $processor
     * @param IndexInterface $index
     * @return $this
     */
    public function loadIntoProcessor($handleName, ProcessorInterface $processor, IndexInterface $index)
    {
        if ($this->isLoaded($handleName)) {
            return $this;
        }
        
        $items = $this->load($handleName, $index);
        foreach ($items as $item) {
            $processor->addItem($item);
        }
        return $this;
    }

    /**
     * Resets state of loader
     *
     * @return $this
     */
    public function reset()
    {
        $this->loaded = array();
        return $this;
    }
}
