<?php
namespace Services\Transcription\Form;

use Laminas\Form\Element as LaminasElement;
use Laminas\Form\Form;
use Omeka\Form\Element as OmekaElement;

class DoTranscribeForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm transcribe pages', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
