<?php

expect()->extend('storeAvatarFile', function ($avatar, $field, $item) {

    fakeRequest(method: 'POST', parameters: [
        'avatar' => $avatar,
    ]);

    $field->save($item);

    return expect($item->avatar)
        ->toBe('files/'.$avatar->hashName());
});
