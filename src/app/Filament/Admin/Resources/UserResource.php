<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Concerns\HasSimmagResourceAccess;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;


class UserResource extends Resource
{
    use HasSimmagResourceAccess;


    protected static array $navigationRoles = ['admin'];

    protected static array $viewAnyRoles = ['admin'];

    protected static array $createRoles = ['admin'];

    protected static array $viewRoles = ['admin'];

    protected static array $editRoles = ['admin'];

    protected static array $deleteRoles = ['admin'];

    protected static bool $useRoleRecordScope = false;



    protected static ?string $model = User::class;



    protected static ?string $navigationIcon =
        'heroicon-o-user-group';



    protected static ?string $navigationGroup =
        'Sistem';



    protected static ?string $navigationLabel =
        'Manajemen User';



    protected static ?string $modelLabel =
        'User';



    protected static ?string $pluralModelLabel =
        'Users';



    protected static ?int $navigationSort = 1;




    public static function getNavigationBadge(): ?string
    {
        return (string) User::count();
    }




    public static function form(Form $form): Form
    {

        return $form->schema([



            Forms\Components\Section::make('Informasi Pengguna')

                ->schema([


                    Forms\Components\FileUpload::make('avatar_url')

                        ->label('Foto Profil')

                        ->image()

                        ->avatar()

                        ->directory('avatars'),




                    Forms\Components\TextInput::make('name')

                        ->label('Nama Lengkap')

                        ->required()

                        ->maxLength(255),





                    Forms\Components\TextInput::make('email')

                        ->email()

                        ->required()

                        ->unique(ignoreRecord:true),




                    Forms\Components\TextInput::make('phone')

                        ->label('Nomor Telepon')

                        ->tel(),




                ])

                ->columns(2),





            Forms\Components\Section::make('Identitas Akademik')

                ->schema([



                    Forms\Components\TextInput::make('identifier')

                        ->label('Identifier'),




                    Forms\Components\TextInput::make('nim')

                        ->label('NIM'),




                    Forms\Components\TextInput::make('nidn')

                        ->label('NIDN'),




                    Forms\Components\TextInput::make('nip')

                        ->label('NIP'),




                    Forms\Components\Select::make('program_study_id')

                        ->relationship(
                            'programStudy',
                            'name'
                        )

                        ->searchable()

                        ->preload(),




                    Forms\Components\TextInput::make('institution_name')

                        ->label('Institusi'),



                ])

                ->columns(2),





            Forms\Components\Section::make('Akses Sistem')

                ->schema([



                    Forms\Components\Select::make('roles')

                        ->relationship(
                            'roles',
                            'name'
                        )

                        ->multiple()

                        ->preload()

                        ->searchable()

                        ->required(),





                    Forms\Components\Toggle::make('is_active')

                        ->label('Akun Aktif')

                        ->default(true),




                ])

                ->columns(2),






            Forms\Components\Section::make('Password')

                ->schema([



                    Forms\Components\TextInput::make('password')

                        ->password()

                        ->confirmed()

                        ->dehydrateStateUsing(
                            fn($state)=>
                            filled($state)
                            ? Hash::make($state)
                            : null
                        )

                        ->dehydrated(
                            fn($state)=>filled($state)
                        )

                        ->required(
                            fn($context)=>
                            $context==='create'
                        ),




                    Forms\Components\TextInput::make('password_confirmation')

                        ->password(),



                ])

                ->columns(2),



        ]);

    }







    public static function table(Table $table): Table
    {

        return $table

            ->columns([



                Tables\Columns\ImageColumn::make('avatar_url')

                    ->label('Foto')

                    ->circular(),





                Tables\Columns\TextColumn::make('name')

                    ->label('Nama')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),




                Tables\Columns\TextColumn::make('identifier')

                    ->label('Identifier')

                    ->searchable(),





                Tables\Columns\TextColumn::make('roles.name')

                    ->label('Role')

                    ->badge()

                    ->colors([

                        'primary'=>'admin',

                        'success'=>'mahasiswa',

                        'warning'=>'dosen_pembimbing',

                        'info'=>'pembimbing_lapangan',

                    ]),




                Tables\Columns\IconColumn::make('is_active')

                    ->label('Status')

                    ->boolean(),




                Tables\Columns\TextColumn::make('created_at')

                    ->label('Terdaftar')

                    ->dateTime('d M Y')

                    ->sortable(),



            ])





            ->filters([


                Tables\Filters\SelectFilter::make('roles')

                    ->relationship(
                        'roles',
                        'name'
                    ),



                Tables\Filters\TernaryFilter::make('is_active')

                    ->label('Status Akun'),


            ])





            ->actions([

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])





            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                ]),

            ]);

    }







    public static function getRelations(): array
    {
        return [];
    }






    public static function getPages(): array
    {

        return [

            'index'=>Pages\ListUsers::route('/'),

            'create'=>Pages\CreateUser::route('/create'),

            'edit'=>Pages\EditUser::route('/{record}/edit'),

        ];

    }

}