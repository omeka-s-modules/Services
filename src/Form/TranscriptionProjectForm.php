<?php
namespace Services\Form;

use Laminas\Form\Element as LaminasElement;
use Laminas\Form\Form;
use Omeka\Form\Element as OmekaElement;

class TranscriptionProjectForm extends Form
{
    public function init()
    {
        $transcriptionProject = $this->getOption('transcription_project');

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
