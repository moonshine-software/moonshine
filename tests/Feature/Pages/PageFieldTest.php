<?php

use MoonShine\Fields\Email;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Password;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Models\MoonshineUserRole;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Pages\Crud\ShowPage;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('pages-feature');

beforeEach(function (): void {

    MoonShineUser::factory()->count(10)->create();

    MoonshineUser::factory()->create([
        'id' => 12,
        'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
        'name' => 'Test Name',
        'email' => 'test@mail.ru',
        'password' => bcrypt('test'),
    ]);

    $this->resource = TestResourceBuilder::new(
        MoonShineUser::class
    )
        ->setTestFields(
            [
            Text::make('Name'),
            Email::make('Email'),
            Password::make('Password'),
            NoInput::make('Badge')->badge(fn () => 'red'),
        ]
        );
});

it('fields on index', function () {
    asAdmin()->get(
        to_page($this->resource, IndexPage::class)
    )
        ->assertOk()
        ->assertSee('table')
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Badge')
        ->assertSee('badge-red')
    ;
});

it('fields on form', function () {
    asAdmin()->get(
        to_page($this->resource, FormPage::class, ['resourceItem' => 12])
    )
        ->assertOk()
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Password')
        ->assertSee('Test Name')
        ->assertSee('test@mail.ru')
    ;
});

it('fields on show', function () {
    asAdmin()->get(
        to_page($this->resource, ShowPage::class, ['resourceItem' => 12])
    )
        ->assertOk()
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Password')
        ->assertSee('Badge')
        ->assertSee('red')
        ->assertSee('Test Name')
        ->assertSee('test@mail.ru')
    ;
});
