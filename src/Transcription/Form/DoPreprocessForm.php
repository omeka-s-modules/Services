<?php
namespace Services\Transcription\Form;

use Laminas\Form\Form;

class DoPreprocessForm extends Form
{
    public function init()
    {
        $project = $this->getOption('project');

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Confirm create pages', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
    }
}
