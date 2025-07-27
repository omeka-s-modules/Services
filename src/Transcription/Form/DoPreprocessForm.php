<?php
namespace Services\Transcription\Form;

use Laminas\Form\Element as LaminasElement;
use Laminas\Form\Form;
use Omeka\Form\Element as OmekaElement;

class DoPreprocessForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm preprocess items', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
