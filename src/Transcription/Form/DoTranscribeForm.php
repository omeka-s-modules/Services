<?php
namespace Services\Transcription\Form;

use Laminas\Form\Form;

class DoTranscribeForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm request transcriptions', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
