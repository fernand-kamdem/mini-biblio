<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use LogicException;
use Modules\Biblio\Models\Author;
use Modules\Biblio\Models\Book;
use Modules\Biblio\Models\Category;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;
use Modules\Emprunt\Notifications\EmpruntApproveNotification;
use Modules\Emprunt\Notifications\EmpruntSoumisNotification;
use Modules\Emprunt\Services\EmpruntWorkflowService;

/**
 * Données de la démonstration publique : un vrai catalogue (20 titres), des membres fictifs
 * et des emprunts datés par rapport au jour du chargement, pour que la démo montre toujours
 * des demandes en attente, des emprunts en cours et des retards.
 *
 * Suppose les rôles déjà créés (RolesPermissionsSeeder).
 */
class DemoSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private const CATEGORIES = [
        'Littérature africaine' => 'Romans et récits d’auteurs africains.',
        'Romans classiques' => 'Les grands classiques étudiés au lycée.',
        'Jeunesse' => 'Lectures pour les plus jeunes.',
        'Sciences humaines' => 'Histoire, société, philosophie.',
        'Science-fiction' => 'Anticipation et mondes imaginaires.',
    ];

    /**
     * Titre, prénom et nom de l'auteur, année, catégorie, exemplaires, résumé.
     *
     * @var list<array{0: string, 1: string, 2: string, 3: int, 4: string, 5: int, 6: string}>
     */
    private const LIVRES = [
        ['Une vie de boy', 'Ferdinand', 'Oyono', 1956, 'Littérature africaine', 4, 'Le journal de Toundi, boy au service d’un commandant dans le Cameroun colonial.'],
        ['Le vieux nègre et la médaille', 'Ferdinand', 'Oyono', 1956, 'Littérature africaine', 3, 'Meka reçoit une médaille de l’administration coloniale et en découvre le prix.'],
        ['Ville cruelle', 'Mongo', 'Beti', 1954, 'Littérature africaine', 3, 'Banda, jeune paysan, affronte la ville de Tanga et ses injustices.'],
        ['Le pauvre Christ de Bomba', 'Mongo', 'Beti', 1956, 'Littérature africaine', 2, 'La tournée d’un missionnaire, racontée par son jeune boy Denis.'],
        ['L’Enfant noir', 'Camara', 'Laye', 1953, 'Littérature africaine', 4, 'Le récit d’une enfance en Haute-Guinée, entre traditions et école.'],
        ['Une si longue lettre', 'Mariama', 'Bâ', 1979, 'Littérature africaine', 3, 'Ramatoulaye écrit à son amie Aïssatou après la mort de son mari.'],
        ['Les Soleils des indépendances', 'Ahmadou', 'Kourouma', 1968, 'Littérature africaine', 2, 'Fama, prince malinké déchu, dans l’Afrique des lendemains d’indépendance.'],
        ['L’Aventure ambiguë', 'Cheikh Hamidou', 'Kane', 1961, 'Littérature africaine', 2, 'Samba Diallo, entre l’école coranique et l’école occidentale.'],
        ['Le monde s’effondre', 'Chinua', 'Achebe', 1958, 'Littérature africaine', 3, 'Okonkwo et l’arrivée des missionnaires et des colons en pays igbo.'],
        ['Americanah', 'Chimamanda Ngozi', 'Adichie', 2013, 'Littérature africaine', 2, 'Ifemelu quitte le Nigeria pour les États-Unis, puis revient.'],
        ['L’Étranger', 'Albert', 'Camus', 1942, 'Romans classiques', 3, 'Meursault, un homme indifférent aux conventions, face à son procès.'],
        ['La Peste', 'Albert', 'Camus', 1947, 'Romans classiques', 2, 'Une épidémie frappe Oran ; chacun choisit comment y faire face.'],
        ['Les Misérables', 'Victor', 'Hugo', 1862, 'Romans classiques', 2, 'Le destin de Jean Valjean, ancien forçat, dans la France du XIXe siècle.'],
        ['Madame Bovary', 'Gustave', 'Flaubert', 1857, 'Romans classiques', 2, 'Emma Bovary rêve d’une autre vie que celle de la province.'],
        ['Germinal', 'Émile', 'Zola', 1885, 'Romans classiques', 3, 'Une grève de mineurs dans le nord de la France.'],
        ['Candide', '', 'Voltaire', 1759, 'Sciences humaines', 3, 'Un conte philosophique qui interroge l’optimisme.'],
        ['Sapiens', 'Yuval Noah', 'Harari', 2011, 'Sciences humaines', 1, 'Une brève histoire de l’humanité.'],
        ['Le Petit Prince', 'Antoine', 'de Saint-Exupéry', 1943, 'Jeunesse', 5, 'Un aviateur rencontre un petit prince venu d’une autre planète.'],
        ['1984', 'George', 'Orwell', 1949, 'Science-fiction', 3, 'Winston Smith dans un monde sous surveillance permanente.'],
        ['Dune', 'Frank', 'Herbert', 1965, 'Science-fiction', 1, 'Paul Atréides sur Arrakis, la planète des sables.'],
    ];

    /**
     * Membres fictifs. Le premier est le compte public « membre » de la démo.
     *
     * @var list<string>
     */
    private const MEMBRES = [
        'Aïcha Ndiaye', 'Jean-Paul Mbarga', 'Clara Ngono', 'Samuel Etoga', 'Fatou Diallo',
        'Lucas Tchamba', 'Inès Kouam', 'Marc Fotso', 'Grace Ebanda', 'Yannick Nkou',
    ];

    /**
     * Membre, livre, état, demandé il y a (jours), emprunté il y a (jours), rendu il y a (jours), note.
     *
     * @var list<array{0: string, 1: string, 2: StatutEmprunt, 3: int, 4: ?int, 5: ?int, 6: ?string}>
     */
    private const EMPRUNTS = [
        ['Lucas Tchamba', 'Madame Bovary', StatutEmprunt::Rendu, 38, 37, 24, null],
        ['Aïcha Ndiaye', 'La Peste', StatutEmprunt::Rendu, 33, 32, 19, null],
        ['Yannick Nkou', 'L’Enfant noir', StatutEmprunt::Rendu, 26, 25, 9, 'Rendu avec deux jours de retard.'],
        ['Clara Ngono', 'Les Misérables', StatutEmprunt::Approuve, 20, 19, null, null],
        ['Jean-Paul Mbarga', '1984', StatutEmprunt::Approuve, 19, 18, null, null],
        ['Aïcha Ndiaye', 'Le vieux nègre et la médaille', StatutEmprunt::Approuve, 17, 16, null, null],
        ['Samuel Etoga', 'Sapiens', StatutEmprunt::Refuse, 12, null, null, 'Exemplaire unique, réservé à la consultation sur place.'],
        ['Fatou Diallo', 'Une vie de boy', StatutEmprunt::Approuve, 10, 9, null, null],
        ['Aïcha Ndiaye', 'Le monde s’effondre', StatutEmprunt::Approuve, 8, 7, null, 'Exemplaire à retirer à l’accueil.'],
        ['Marc Fotso', 'L’Étranger', StatutEmprunt::Approuve, 6, 5, null, null],
        ['Grace Ebanda', 'Ville cruelle', StatutEmprunt::Approuve, 4, 3, null, null],
        ['Lucas Tchamba', 'Dune', StatutEmprunt::Approuve, 3, 2, null, null],
        ['Inès Kouam', 'Americanah', StatutEmprunt::EnAttente, 2, null, null, null],
        ['Samuel Etoga', 'Le Petit Prince', StatutEmprunt::EnAttente, 1, null, null, null],
        ['Aïcha Ndiaye', 'Une si longue lettre', StatutEmprunt::EnAttente, 0, null, null, null],
        ['Aïcha Ndiaye', 'Candide', StatutEmprunt::Brouillon, 0, null, null, null],
    ];

    public function run(): void
    {
        $livres = $this->creerCatalogue();
        $comptes = $this->creerComptesDemo();
        $membres = $this->creerMembres($comptes['membre']);

        $this->creerEmprunts($livres, $membres, $comptes['bibliothecaire']);
    }

    /**
     * Livres indexés par titre (« 1984 » devient une clé entière en PHP).
     *
     * @return array<int|string, Book>
     */
    private function creerCatalogue(): array
    {
        $categories = collect(self::CATEGORIES)
            ->map(fn (string $description, string $nom): Category => Category::create(['nom' => $nom, 'description' => $description]));

        $auteurs = [];
        $livres = [];

        foreach (self::LIVRES as [$titre, $prenom, $nom, $annee, $categorie, $exemplaires, $resume]) {
            $auteur = $auteurs[$prenom.' '.$nom] ??= Author::create(['prenom' => $prenom, 'nom' => $nom]);

            $livre = Book::create([
                'category_id' => $categories[$categorie]->id,
                'titre' => $titre,
                'annee_parution' => $annee,
                'stock_total' => $exemplaires,
                'stock_disponible' => $exemplaires,
                'description' => $resume,
            ]);
            $livre->authors()->attach($auteur);

            $livres[$titre] = $livre;
        }

        return $livres;
    }

    /**
     * Les trois comptes publics de config/demo.php, tous avec le même mot de passe.
     *
     * @return array<string, User>
     */
    private function creerComptesDemo(): array
    {
        return collect(config()->array('demo.accounts'))
            ->map(function (array $compte): User {
                $user = User::create([
                    'name' => $compte['name'],
                    'email' => $compte['email'],
                    'password' => config('demo.password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'language' => 'fr',
                ]);
                $user->assignRole($compte['role']);

                return $user;
            })
            ->all();
    }

    /**
     * @return array<string, Member>
     */
    private function creerMembres(User $compteMembre): array
    {
        $membres = [];

        foreach (self::MEMBRES as $rang => $nom) {
            $user = $rang === 0 ? $compteMembre : tap(User::create([
                'name' => $nom,
                'email' => Str::slug($nom, '.').'@example.com',
                'password' => Str::password(32),
                'email_verified_at' => now(),
                'is_active' => true,
                'language' => 'fr',
            ]))->assignRole('MEMBRE');

            $membres[$nom] = Member::create([
                'user_id' => $user->id,
                'card_number' => Member::generateCardNumber(),
                'phone' => sprintf('+237 6%02d %02d %02d %02d', 70 + $rang, 12 + $rang, 34 + $rang, 50 + $rang),
                'registration_date' => today()->subDays(90 - $rang * 5)->toDateString(),
            ]);
        }

        return $membres;
    }

    /**
     * Crée les emprunts dans leur état final, ajuste le stock des emprunts en cours
     * et dépose les notifications que le workflow aurait envoyées.
     *
     * @param  array<int|string, Book>  $livres
     * @param  array<string, Member>  $membres
     */
    private function creerEmprunts(array $livres, array $membres, User $bibliothecaire): void
    {
        $equipe = User::role(['ADMIN', 'BIBLIOTHECAIRE'])->get();

        foreach (self::EMPRUNTS as [$nomMembre, $titre, $statut, $demandeIlYa, $empruntIlYa, $renduIlYa, $note]) {
            $livre = $livres[$titre] ?? throw new LogicException("Livre absent du catalogue de démonstration : {$titre}");
            $demandeLe = $demandeIlYa === 0 ? now()->subHour() : now()->subDays($demandeIlYa)->setTime(9, 30);
            $empruntLe = $empruntIlYa !== null ? today()->subDays($empruntIlYa) : null;

            $emprunt = new Emprunt([
                'book_id' => $livre->id,
                'member_id' => $membres[$nomMembre]->id,
                'statut' => $statut,
                'workflow_state' => $statut->value,
                'date_retour_souhaitee' => ($empruntLe ?? $demandeLe)->copy()->addDays(EmpruntWorkflowService::DUREE_EMPRUNT_JOURS)->toDateString(),
                'date_emprunt' => $empruntLe?->toDateString(),
                'date_retour_prevue' => $empruntLe?->copy()->addDays(EmpruntWorkflowService::DUREE_EMPRUNT_JOURS)->toDateString(),
                'date_retour_reelle' => $renduIlYa !== null ? today()->subDays($renduIlYa)->toDateString() : null,
                'approuve_par' => $empruntLe ? $bibliothecaire->id : null,
                'recu_par' => $renduIlYa !== null ? $bibliothecaire->id : null,
                'note_bibliothecaire' => $note,
            ]);
            $emprunt->setCreatedAt($demandeLe)->setUpdatedAt($demandeLe)->save();

            if ($statut === StatutEmprunt::Approuve) {
                $livre->decrement('stock_disponible');
            }

            if ($statut === StatutEmprunt::EnAttente) {
                Notification::sendNow($equipe, new EmpruntSoumisNotification($emprunt));
            }

            if ($statut === StatutEmprunt::Approuve && $empruntLe->gte(today()->subDays(7))) {
                Notification::sendNow($membres[$nomMembre]->user, new EmpruntApproveNotification($emprunt));
            }
        }
    }
}
