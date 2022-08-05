<?php

namespace Statamic\Eloquent\Import;

use Statamic\Contracts\Forms\Form;
use Statamic\Facades\File;
use Statamic\Facades\Folder;
use Statamic\Facades\YAML;

class FormSubmissions
{
    public static function fileSubmissionsForForm(Form $form)
    {
        $path = config('statamic.forms.submissions') . '/' . $form->handle();

        return collect(Folder::getFilesByType($path, 'yaml'))->map(function ($file) use ($form) {
            return $form->makeSubmission()
                ->id(pathinfo($file)['filename'])
                ->data(YAML::parse(File::get($file)));
        });
    }
}
