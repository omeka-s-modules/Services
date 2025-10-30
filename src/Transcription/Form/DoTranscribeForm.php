<?php
namespace Services\Transcription\Form;

use Laminas\Form\Form;

class DoTranscribeForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'select',
            'name' => 'action',
            'options' => [
                'label' => 'Transcribe action', // @translate
                'info' => 'Select an additional action to perform when transcribing pages.', // @translate
                'empty_option' => 'Default action', // @translate
                'value_options' => [
                    'transcribe_failed' => 'Re-attempt failed transcriptions', // @translate
                    'transcribe_all' => 'Re-transcribe all pages', // @translate
                ],
            ],
        ]);
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm request transcriptions', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'action',
            'required' => false,
        ]);
    }
}
