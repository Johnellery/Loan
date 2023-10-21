<?php

namespace App\Livewire;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use App\Models\Philprovince;
use App\Models\Philmuni;
use App\Models\Philbrgy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Squire\Models\Country;
use Livewire\Component;
class item extends Component
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $activeNavigationIcon = 'heroicon-s-map-pin';
    protected static ?int $navigationSort = 11;
    protected static ?string $navigationGroup = 'Set up';



}
