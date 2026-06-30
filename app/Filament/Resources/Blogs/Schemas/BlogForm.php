<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Models\Blog;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('title_bng')
                    ->default(null),
                TextInput::make('slug')
                    ->required(),
                RichEditor::make('content')
                    ->label('English Content')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 200px;',
                    ]),
                RichEditor::make('summary')
                    ->label('English Summary')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 150px;',
                    ]),
                RichEditor::make('excerpt')
                    ->label('Excerpt')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 100px;',
                    ]),

                RichEditor::make('content_bng')
                    ->label('Bangla Content')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 200px;',
                    ]),
                RichEditor::make('summary_bng')
                    ->label('Bangla Summary')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 150px;',
                    ]),


                FileUpload::make('featured_image')
                    ->image()
                    ->disk('public')
                    ->directory('blogs'),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Blog::$categories)
                    ->required()
                    ->searchable(),
                Hidden::make('author_id')
                    ->default(fn() => Auth::id()),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published'])
                    ->default('draft')
                    ->required(),
                TextInput::make('meta_title')
                    ->default(null),
                RichEditor::make('meta_description')
                    ->label('Meta Description')
                    ->columnSpanFull()
                    ->extraInputAttributes([
                        'style' => 'min-height: 150px;',
                    ]),
                TextInput::make('views')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_featured')
                    ->required(),
                TextInput::make('reading_time')
                    ->numeric()
                    ->default(null),
                DateTimePicker::make('published_at'),
            ]);
    }
}
