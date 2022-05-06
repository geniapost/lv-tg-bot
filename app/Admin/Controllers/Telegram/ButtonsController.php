<?php

namespace App\Admin\Controllers\Telegram;

use App\Models\Telegram\Button;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Support\Facades\URL;

class ButtonsController extends AdminController
{
    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form($id)->edit($id));
    }

    public function update($id)
    {
        return $this->form($id)->update($id);
    }

    public function grid()
    {
        $grid = new Grid(new Button());

        $grid->column('title', 'Назва');
        $grid->column('full_name', 'Відноситься до')->display(function () {
            return $this->parent_id ? Button::find($this->parent_id)->title : '-';
        });

        return $grid;
    }

    public function form($id)
    {
        $model = Button::class;

        $form = new Form(new $model());

        $buttonsTable = (new $model)->getTable();
        $connection = config('admin.database.connection');

        $button = Button::findOrFail($id);

        $form->text('title', 'Назва')
            ->creationRules(['required', "unique:{$connection}.{$buttonsTable}"])
            ->updateRules(['required', "unique:{$connection}.{$buttonsTable},title,{{id}}"]);

        $form->textarea('text', 'Зміст')->rules('required');
        $form->select('parent_id', 'Відноситься до')->options(Button::all()->pluck('title', 'id'));

        $form->select('before', 'Вставити перед')->options($button->getNeighbors());
        $form->select('after', 'Поставити після')->options($button->getNeighbors());

        $form->image('image', 'Зображення')->removable();
        $form->file('file', 'Файл')->removable();

        $form->saving(function (Form $form) use($button){
            if($form->after)
            {
                $after = Button::findOrFail($form->after);
                $after->afterNode($button)->save();
            }

            if($form->before)
            {
                $before = Button::findOrFail($form->before);
                $before->beforeNode($button)->save();
            }

            foreach ($form->fields() as $key => $field)
            {
                if(in_array($field->column(), ['after', 'before']))
                {
                    $form->fields()->forget($key);
                }
            }
        });

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