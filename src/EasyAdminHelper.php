<?php

namespace App;

use Symfony\Component\Validator\Constraints\File;

class EasyAdminHelper
{
    public static function getFileInputAttributes(object $entity, string $key)
    {
        $refl = new \ReflectionProperty($entity, $key);
        $attr = [];
        foreach ($refl->getAttributes() as $attribute) {
            if (File::class === $attribute->getName()) {
                foreach ($attribute->getArguments() as $name => $value) {
                    if ('mimeTypes' === $name) {
                        $attr['accept'] = implode(',', $value);
                    }
                }
            }
        }

        return $attr;
    }
}
