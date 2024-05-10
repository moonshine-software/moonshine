<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Image;
use MoonShine\Http\Requests\ProfileFormRequest;
use MoonShine\Pages\ProfilePage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileController extends MoonShineController
{
    /**
     * @throws Throwable
     */
    public function store(ProfileFormRequest $request): Response
    {
        $page = moonshineConfig()->getPage('profile', ProfilePage::class);
        $fields = Fields::make($page->fields());

        /** @var Image $image */
        $image = $fields
            ->onlyFields()
            ->findByClass(Image::class);

        $data = $request->validated();

        $resultData = [
            moonshineConfig()->getUserField(
                'username',
                'email'
            ) => $data['username'],
            moonshineConfig()->getUserField('name') => $data['name'],
        ];

        if (isset($data['password']) && filled($data['password'])) {
            $resultData[moonshineConfig()->getUserField('password')] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }


        if(! is_null($image)) {
            $this->applyImage($image, $resultData);
        }

        $resultData = array_filter(
            $resultData,
            static fn ($key): bool => $key !== 0 && $key !== '',
            ARRAY_FILTER_USE_KEY
        );

        $request->user()->update($resultData);

        if ($request->ajax()) {
            return $this->json(message: __('moonshine::ui.saved'));
        }

        $this->toast(
            __('moonshine::ui.saved'),
            'success'
        );

        return back();
    }

    private function applyImage(Image $image, array &$result): void
    {
        $avatarColumn = moonshineConfig()->getUserField('avatar');

        $avatar = request()->file('avatar');
        $oldAvatar = request()->get('hidden_avatar', '');
        $currentAvatar = data_get(request()->user(), $avatarColumn, '');

        if (! is_null($avatar)) {
            $result[$avatarColumn] = $image->store($avatar);
            $image->deleteFile($oldAvatar);

            return;
        }

        $result[$avatarColumn] = $oldAvatar;

        if($oldAvatar !== $currentAvatar) {
            $image->deleteFile($currentAvatar);
        }
    }
}
