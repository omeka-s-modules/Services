<?php
namespace Services\Transcription\Form;

use Laminas\Form\Form;

class DoFetchForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm fetch transcriptions', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
