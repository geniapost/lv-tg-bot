<?php

namespace App\Admin\Controllers\Telegram;

use App\Models\Telegram\Button;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class ButtonsController extends AdminController
{
    public function grid()
    {
        $grid = new Grid(new Button());

        $grid->column('title', 'Назва');

        return $grid;
    }

    public function form()
    {
        $model = Button::class;

        $form = new Form(new $model());

        $buttonsTable = (new $model)->getTable();
        $connection = config('admin.database.connection');

        $form->text('title', 'Назва')
            ->creationRules(['required', "unique:{$connection}.{$buttonsTable}"])
            ->updateRules(['required', "unique:{$connection}.{$buttonsTable},title,{{id}}"]);

        $form->textarea('text', 'Зміст')->rules('required');

        $form->select('parent_id', 'Відноситься до')->options(Button::all()->pluck('title', 'id'));
        $form->image('image', 'Зображення');

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }

//    public function index(Content $content)
//    {
//
//    }

//    public function show($id, Content $content)
//    {
//
//    }
}