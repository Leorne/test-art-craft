<?php

namespace App\Helpers;

use App\Http\Models\User;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Validator
{
    public $errors = [];

    public function validate(array $filters)
    {
        foreach ($filters as $value => $filter) {
            $this->$filter($value);
        }
        return $this->errors;
    }


    protected function email(string $email)
    {
        $this->validateEmail($email) ?: $this->errors['email'][] = 'Email format is not correct!';
        $this->uniqueEmail($email) ?: $this->errors['email'][] = 'Email is not unique!';
    }


    protected function name($name)
    {
        $this->nameRequired($name) ?: $this->errors['name'][] = 'Name is required field!';
        $this->nameMaxLength($name, 25) ?: $this->errors['name'][] = 'Name size is 25 letters maximum!';
        $this->nameMinLength($name, 3) ?: $this->errors['name'][] = 'Name size is 3 letters minimum!';
        $this->nameCorrect($name) ?: $this->errors['name'][] = 'Name should contain only letters and space!';
    }

    protected function image(UploadedFile $image)
    {
        $image->isValid() ?: $this->errors['photo'][] = 'Something went wrong..';
        $this->imageType($image, ['png', 'jpg', 'jpeg']) ?: $this->errors['photo'][] = 'File type should be png or jpg';
        $this->maxSize($image, 500000) ?: $this->errors['photo'][] = 'File size larger then 500KB.';
    }


    protected function imageType(UploadedFile $item, array $types = [])
    {
        return array_key_exists($item->getClientOriginalExtension(), $types) ? true : false;
    }

    protected function maxSize(UploadedFile $file, $maxSize)
    {
        return ($file->getSize() < $maxSize) ? true : false;
    }

    protected function uniqueEmail(string $email)
    {
        return User::where('email', '=', $email)->count() < 1 ? true : false;
    }

    protected function validateEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
    }

    protected function nameRequired($name)
    {
        return ($name !== '') ? true : false;
    }

    protected function nameCorrect($name)
    {
        if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
            return false;
        }
        return preg_match("/^[a-zA-Z А-ЯЁа-яё]*$/u", $name) ? true : false;
    }

    protected function nameMaxLength(string $name, int $max)
    {
        return mb_strlen($name) < $max ? true : false;
    }

    protected function nameMinLength(string $name, int $min)
    {
        return mb_strlen($name) > $min ? true : false;
    }


}