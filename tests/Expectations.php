<?php

use Pest\Expectation;

expect()->extend('storeAvatarFile', function ($avatar, $field, $item): Expectation {

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $field->save($item);

    return expect($item->avatar)
        ->toBe('files/' . $avatar->hashName());
});
