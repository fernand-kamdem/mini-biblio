<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;
use Modules\Biblio\Models\Book;
use App\Models\User;


class Dashboard extends Component
{
    
    public function render(){
        $user=Auth::user();
        $totalPendingLoans=Emprunt::query()->where('statut', 'en_attente')->count();
        $totalLateLoans=Emprunt::query()->where('date_retour_prevue', '<', now())->where('statut', 'approuve')->count();;
        $totalApproveLoans=Emprunt::query()->where('statut', 'approuve')->count();;
        $totalMembers=Member::count();
        $totalLibrarians=User::query()->role('BIBLIOTHECAIRE')->count();
        $totalBooks=Book::count();
        $pendingLoans=Emprunt::with(['member.user', 'book'])->where('statut', 'en_attente')->limit(4)->orderBy('created_at', 'desc')->get();
        $lateLoans=Emprunt::with(['member.user', 'book'])->where('date_retour_prevue', '<', now())->limit(4)->orderBy('created_at', 'desc')->get();
        $lowStock=Book::query()->where('stock_disponible', '<', 5)->limit(5)->orderBy('stock_disponible', 'asc')->get();
        
        //memeber dasboard
        $myActiveLoans=Emprunt::where(
            [
                'member_id'=>$user?->member?->id,
                'statut' => 'approuve'
            ]
        )->get();
        
        $myLateLoans=Emprunt::where('member_id', $user?->member?->id)
            ->where('statut', 'approuve')
            ->where('date_retour_prevue', '<', now())
            ->get();

        return view('livewire.dashboard', [
            'user' => $user,
            'totalPendingLoans' => $totalPendingLoans,
            'totalLateLoans' => $totalLateLoans,
            'totalApproveLoans' => $totalApproveLoans,
            'totalBooks' => $totalBooks,
            'totalMembers' => $totalMembers,
            'totalLibrarians' => $totalLibrarians,
            'pendingLoans' => $pendingLoans,
            'lateLoans' => $lateLoans,
            'lowStock' => $lowStock,
            'myActiveLoans' => $myActiveLoans,
            'myLateLoans' => $myLateLoans,
        ]);
    }
}

?>