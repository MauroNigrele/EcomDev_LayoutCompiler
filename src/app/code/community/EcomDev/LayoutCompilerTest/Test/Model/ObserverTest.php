<?php

class EcomDev_LayoutCompilerTest_Test_Model_ObserverTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Observer instance
     *
     * @var EcomDev_LayoutCompiler_Model_Observer
     */
    private $observer;

    protected function setUp()
    {
        $this->observer = new EcomDev_LayoutCompiler_Model_Observer();
    }

    public function testItSpecifiesDefaultConfigurationForClassAliasesAndDefaultSavePath()
    {
        $factory = $this->getMockForAbstractClass('EcomDev_LayoutCompiler_Contract_FactoryInterface');
        $factory->expects($this->exactly(15))
            ->method('setClassAlias')
            ->withConsecutive(
                array('layout', 'ecomdev_layoutcompiler/layout'),
                array('layout_update', 'ecomdev_layoutcompiler/layout_update'),
                array('layout_processor', 'EcomDev_LayoutCompiler_Layout_Processor'),
                array('layout_loader', 'EcomDev_LayoutCompiler_Layout_Loader'),
                array('layout_source_file', 'EcomDev_LayoutCompiler_Layout_Source_File'),
                array('layout_source_string', 'EcomDev_LayoutCompiler_Layout_Source_String'),
                array('layout_source_database', 'ecomdev_layoutcompiler/layout_source_database'),
                array('compiler', 'EcomDev_LayoutCompiler_Compiler'),
                array('compiler_metadata_factory', 'EcomDev_LayoutCompiler_Compiler_MetadataFactory'),
                array('compiler_parser_handle', 'EcomDev_LayoutCompiler_Compiler_Parser_Handle'),
                array('compiler_parser_reference', 'EcomDev_LayoutCompiler_Compiler_Parser_Reference'),
                array('compiler_parser_remove', 'EcomDev_LayoutCompiler_Compiler_Parser_Remove'),
                array('compiler_parser_block', 'ecomdev_layoutcompiler/compiler_parser_block'),
                array('compiler_parser_action', 'ecomdev_layoutcompiler/compiler_parser_action'),
                array('cache', 'ecomdev_layoutcompiler/cache')
            )
            ->willReturnSelf()
        ;

        $expectedVarDirectory = Mage::getBaseDir('var') . DS . 'ecomdev' . DS . 'layoutcompiler';

        $factory->expects($this->exactly(2))
            ->method('setDependencyInjectionInstruction')
            ->withConsecutive(
                array(
                    'EcomDev_LayoutCompiler_Contract_PathAwareInterface',
                    'setSavePath',
                    $expectedVarDirectory
                ),
                array(
                    'EcomDev_LayoutCompiler_Contract_ExporterAwareInterface',
                    'setExporter',
                    $this->isInstanceOf('EcomDev_LayoutCompiler_Contract_ExporterInterface')
                )
            )
            ->willReturnSelf();

        $observer = $this->generateObserver(array('factory' => $factory), 'ecomdev_layoutcompiler_factory_init');
        $this->observer->onFactoryInitialize($observer);
    }

    public function testItSpecifiesDefaultArgumentForCompilerOnLayoutInitialization()
    {
        $metadataFactory = $this->getMockForAbstractClass('EcomDev_LayoutCompiler_Contract_Compiler_MetadataFactoryInterface');
        $factory = $this->getMockForAbstractClass('EcomDev_LayoutCompiler_Contract_FactoryInterface');


    }
}
