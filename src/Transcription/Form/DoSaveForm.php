<?php
namespace Services\Transcription\Form;

use Laminas\Form\Form;

class DoSaveForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm save transcriptions', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'action',
            'required' => false,
        ]);
    }
}
