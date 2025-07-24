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
            'name' => 'o-module-services:model-id',
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
            'name' => 'o-module-services:access-token',
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

        $inputFilter = $this->getInputFilter();
    }
}
