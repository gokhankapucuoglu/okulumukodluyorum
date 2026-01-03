<?php

namespace App\Filament\Resources\Admin\Schools;

use Tab;
use BackedEnum;
use App\Models\School;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Admin\Schools\Pages\ManageSchools;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;
    protected static ?string $slug = 'okullar';
    protected static ?string $modelLabel = 'Okullar';
    protected static ?string $pluralModelLabel = 'Okullar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Okul Bilgileri')
                    ->tabs([
                        // 1. SEKME: KURUM KİMLİK BİLGİLERİ
                        Tabs\Tab::make('Kurum Kimlik Bilgileri')
                            ->icon('heroicon-m-identification')
                            ->schema([
                                TextInput::make('province')
                                    ->label('İl')
                                    ->required()
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'required' => 'İl bilgisi zorunludur.',
                                    ]),
                                TextInput::make('district')
                                    ->label('İlçe')
                                    ->required()
                                    ->maxLength(100)
                                    ->validationMessages([
                                        'required' => 'İlçe bilgisi zorunludur.',
                                    ]),
                                TextInput::make('institution_code')
                                    ->label('Kurum Kodu')
                                    ->required()
                                    ->tel()
                                    ->placeholder('123456')
                                    ->mask('999999')
                                    ->numeric()
                                    ->length(6)
                                    ->unique(ignoreRecord: true)
                                    ->validationMessages([
                                        'required' => 'Kurum kodu zorunludur.',
                                        'unique' => 'Bu kurum kodu ile kayıtlı başka bir okul var.',
                                    ]),
                                TextInput::make('name')
                                    ->label('Okul Adı')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'Okul adı zorunludur.',
                                    ]),
                            ])->columns(3),

                        // 2. SEKME: EĞİTİM DETAYLARI
                        Tabs\Tab::make('Eğitim Detayları')
                            ->icon('heroicon-m-academic-cap')
                            ->schema([
                                Select::make('type')
                                    ->label('Okul Türü')
                                    ->options([
                                        'Devlet'     => 'Devlet',
                                        'Özel'       => 'Özel',
                                        'Yarı Özel'  => 'Yarı Özel',
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Okul türü zorunludur.',
                                    ]),
                                Select::make('level')
                                    ->label('Okul Seviyesi')
                                    ->options([
                                        'Okul Öncesi' => 'Okul Öncesi',
                                        'İlkokul'     => 'İlkokul',
                                        'Ortaokul'    => 'Ortaokul',
                                        'Lise'        => 'Lise',
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Okul seviyesi zorunludur.',
                                    ]),
                                Select::make('program')
                                    ->label('Uygulanan Program')
                                    ->options([
                                        // İmam Hatip Programları
                                        'Fen ve Sosyal Bilimler Programı' => 'Fen ve Sosyal Bilimler Programı',
                                        'Yabancı Dil Ağırlıklı Program' => 'Yabancı Dil Ağırlıklı Program',
                                        'Hafızlık Pekiştirme Programı' => 'Hafızlık Pekiştirme Programı',
                                        'Geleneksel ve Çağdaş Görsel Sanatlar Programı' => 'Geleneksel ve Çağdaş Görsel Sanatlar Programı',
                                        'Musiki Programı' => 'Musiki Programı',
                                        'Spor Programı' => 'Spor Programı',
                                        'Uluslararası İHL Programı' => 'Uluslararası İHL Programı',

                                        // Mesleki Teknik Programlar
                                        'Anadolu Teknik Programı (ATP)' => 'Anadolu Teknik Programı (ATP)',
                                        'Anadolu Meslek Programı (AMP)' => 'Anadolu Meslek Programı (AMP)',
                                        'MESEM Programı' => 'MESEM Programı',

                                        // Akademik / Genel Programlar
                                        'Fen Lisesi Programı' => 'Fen Lisesi Programı',
                                        'Sosyal Bilimler Lisesi Programı' => 'Sosyal Bilimler Lisesi Programı',
                                        'Hazırlık Sınıfı Programı' => 'Hazırlık Sınıfı Programı',
                                        'IB (Uluslararası Bakalorya)' => 'IB (Uluslararası Bakalorya)',
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // 3. SEKME: ZAMAN ÇİZELGESİ
                        Tabs\Tab::make('Zaman Çizelgesi')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                Select::make('education_time')
                                    ->label('Eğitim Zamanı')
                                    ->options([
                                        'Tam Gün' => 'Tam Gün',
                                        'İkili Eğitim' => 'İkili Eğitim (Sabahçı/Öğlenci)',
                                        'İkinci Öğretim' => 'İkinci Öğretim',
                                    ])
                                    ->native(false)
                                    ->searchable()
                                    ->columnSpanFull(),
                                TimePicker::make('start_time')
                                    ->label('Ders Başlama Saati')
                                    ->seconds(false),
                                TimePicker::make('end_time')
                                    ->label('Ders Bitiş Saati')
                                    ->seconds(false),
                            ])->columns(2),

                        // 4. SEKME: İLETİŞİM BİLGİLERİ
                        Tabs\Tab::make('İletişim Bilgileri')
                            ->icon('heroicon-m-phone')
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Telefon Numarası')
                                    ->tel()
                                    ->mask('(999) 999 99 99')
                                    ->placeholder('(5XX) XXX XX XX')
                                    ->stripCharacters(['(', ')', ' ']),
                                TextInput::make('email')
                                    ->label('E-posta Adresi')
                                    ->email()
                                    ->placeholder('ornek@meb.k12.tr'),
                                Textarea::make('address')
                                    ->label('Adres')
                                    ->rows(3)
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->weight('bold')
                        ->size('lg'),
                    Split::make([
                        TextColumn::make('institution_code')
                            ->icon('heroicon-m-hashtag')
                            ->color('gray')
                            ->grow(false),
                        TextColumn::make('type')
                            ->badge()
                            ->color('info')
                            ->grow(false),
                        TextColumn::make('level')
                            ->badge()
                            ->color('success')
                            ->grow(false),
                    ]),
                ])
                ->space(1),
            ])
            ->actions([
                    EditAction::make()
                        ->label('')
                        ->tooltip('Düzenle')
                        ->size('lg')
                        ->modalHeading(fn ($record) => $record->name . ' Bilgilerini Düzenle'),
                    DeleteAction::make()
                        ->label('')
                        ->tooltip('Sil')
                        ->size('lg')
                        ->modalHeading(fn ($record) => $record->name . ' Siliniyor...'),
                    RestoreAction::make()
                        ->label('')
                        ->tooltip('Geri Yükle')
                        ->size('lg')
                        ->color('success')
                        ->modalHeading(fn ($record) => $record->name . ' Geri Yükleniyor...'),
                    Action::make('visit_site')
                        ->label('')
                        ->tooltip('Okul Sitesine Git')
                        ->size('lg')
                        ->icon('heroicon-o-globe-alt')
                        ->color('info')
                        ->url(fn (School $record) => url('/' . $record->institution_code))
                        ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchools::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
