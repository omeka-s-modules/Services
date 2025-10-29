<?php
namespace Services\Transcription\Form;

use Laminas\Form\Element as LaminasElement;
use Laminas\Form\Form;
use Omeka\Form\Element as OmekaElement;

class ProjectForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => LaminasElement\Text::class,
            'name' => 'o:label',
            'options' => [
                'label' => 'Project label', // @translate
                'info' => 'Enter the label of this transcription project.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => LaminasElement\Text::class,
            'name' => 'o-module-services:model_id',
            'options' => [
                'label' => 'Model ID', // @translate
                'info' => 'Enter the ID of the Transkribus text recognition model. The model cannot be modified once it is set.', // @translate
            ],
            'attributes' => [
                'required' => true,
                'disabled' => $project ? true : false,
            ],
        ]);
        $this->add([
            'type' => LaminasElement\Text::class,
            'name' => 'o-module-services:access_token',
            'options' => [
                'label' => 'Access token', // @translate
                'info' => 'Enter the Mino access token.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => OmekaElement\Query::class,
            'name' => 'o:query',
            'options' => [
                'label' => 'Items query', // @translate
                'info' => 'Configure the items that make up this transcription project. No query means all items.', // @translate
                'query_resource_type' => 'items',
                'query_partial_excludelist' => [
                    'common/advanced-search/sort',
                ],
            ],
        ]);
        $this->add([
            'type' => OmekaElement\PropertySelect::class,
            'name' => 'o:property',
            'options' => [
                'label' => 'Transcription property', // @translate
                'info' => 'Select the property used to store saved transcriptions.', // @translate
            ],
            'attributes' => [
                'class' => 'chosen-select',
            ],
        ]);
        $this->add([
            'type' => LaminasElement\Select::class,
            'name' => 'o-module-services:target',
            'options' => [
                'label' => 'Transcription target', // @translate
                'info' => 'Select the target resources where saved transcriptions will be stored.', // @translate
                'empty_option' => 'Items and media', // @translate
                'value_options' => [
                    'items' => 'Items', // @translate
                    'media' => 'Media', // @translate
                ],
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'o-module-services:target',
            'required' => false,
        ]);
    }
}
