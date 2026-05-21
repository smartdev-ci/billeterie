<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // Événement unique (toujours ID 1)
        $event = Event::findOrFail(1);

        // Autres événements (éditions passées pour la grille)
        $events = collect([
            (object) [
                'id' => 1,
                'edition' => 'Édition 5',
                'title' => 'Le Grand Retour',
                'date' => '28 Juin 2026',
                'time' => '22h00 - 06h00',
                'venue' => 'Palais de la Culture',
                'city' => 'Abidjan, Côte d\'Ivoire',
                'artists' => ['DJ Myst', 'Kerozen', 'Liricson', 'DJ Lewis'],
                'ticketsLeft' => 87,
                'totalTickets' => 500,
                'status' => 'upcoming',
                'img' => 'https://images.pexels.com/photos/1190297/pexels-photo-1190297.jpeg?auto=compress&cs=tinysrgb&w=800',
                'price' => 'À partir de 5 000 FCFA',
                'tags' => ['Afrobeats', 'Drill', 'Amapiano'],
            ],
            (object) [
                'id' => 2,
                'edition' => 'Édition 4',
                'title' => 'Nuit des Champions',
                'date' => '14 Déc 2025',
                'time' => '22h00 - 06h00',
                'venue' => 'Sofitel Abidjan Hôtel Ivoire',
                'city' => 'Abidjan, Côte d\'Ivoire',
                'artists' => ['DJ Myst', 'Serge Beynaud', 'Nash', 'DJ Zeus'],
                'ticketsLeft' => 0,
                'totalTickets' => 400,
                'status' => 'past',
                'img' => 'https://images.pexels.com/photos/2114365/pexels-photo-2114365.jpeg?auto=compress&cs=tinysrgb&w=800',
                'price' => 'Terminé',
                'tags' => ['Coupé-décalé', 'Afrobeats', 'Hip-hop'],
            ],
            (object) [
                'id' => 3,
                'edition' => 'Édition 3',
                'title' => 'Vibes & Paiya',
                'date' => '15 Juin 2025',
                'time' => '22h00 - 06h00',
                'venue' => 'La Villa des Lumières',
                'city' => 'Abidjan, Côte d\'Ivoire',
                'artists' => ['DJ Myst', 'Ariel Sheney', 'Dj Black', 'Master KG'],
                'ticketsLeft' => 0,
                'totalTickets' => 350,
                'status' => 'past',
                'img' => 'https://images.pexels.com/photos/1540406/pexels-photo-1540406.jpeg?auto=compress&cs=tinysrgb&w=800',
                'price' => 'Terminé',
                'tags' => ['Afrobeats', 'Dancehall'],
            ],
        ]);


        // Témoignages mockés (à remplacer par un modèle plus tard)
        $testimonials = collect([
            (object) [
                'quote' => 'Le Petit Poto c\'est une autre dimension. L\'ambiance, les artistes, les lumieres... J\'y étais à l\'édition 4 et j\'ai déjà réservé pour la 5. C\'est obligatoire !',
                'name' => 'Kouame A.',
                'platform' => 'instagram',
                'handle' => 'kouame.abj',
            ],
            (object) [
                'quote' => 'Meilleure soirée de l\'année sans discussion. Le DJ Myst a detruit la place. La section VIP valait chaque franc. Je recommande les yeux fermes.',
                'name' => 'Adjoa M.',
                'platform' => 'tiktok',
                'handle' => 'adjoa.vibes',
            ],
            (object) [
                'quote' => 'Prise de ticket sur le site en 2 minutes, ticket QR reu sur WhatsApp immediatement. L\'experience commence déjà avant la soiree. 10/10',
                'name' => 'Franck K.',
                'platform' => 'twitter',
                'handle' => 'franckk_cl',
            ],
            (object) [
                'quote' => 'Le Petit Poto a redefini ce que c\'est une soiree a Abidjan. La mise en scene, le son, tout etait parfait. La jeunesse ivoirienne meritait ca.',
                'name' => 'Naila B.',
                'platform' => 'instagram',
                'handle' => 'nailab_abj',
            ],
        ]);

        // FAQ mockée
        $faqItems = collect([
            (object) [
                'question' => 'Comment acheter mon billet ?',
                'answer' => 'Clique sur "Reserver", renseigne tes informations, choisis ton moyen de paiement Mobile Money et valide. Ton billet QR arrive par email instantanement.',
            ],
            (object) [
                'question' => 'Comment vais-je recevoir mon billet ?',
                'answer' => 'Ton e-ticket PDF avec QR code securise est envoye a ton email immediatement apres paiement. Tu peux aussi le retrouver dans ton espace si tu es connecte.',
            ],
            (object) [
                'question' => 'Quels modes de paiement sont acceptes ?',
                'answer' => 'Orange Money, MTN Mobile Money, Wave et carte bancaire. Le paiement est securise et confirme en temps reel.',
            ],
            (object) [
                'question' => 'Puis-je me faire rembourser ?',
                'answer' => 'Les billets ne sont pas remboursables sauf annulation officielle de l\'evenement. En cas de force majeure, contacte-nous sur WhatsApp.',
            ],
            (object) [
                'question' => 'Qu\'est-ce que le ticket VIP inclut exactement ?',
                'answer' => 'Acces prioritaire, zone VIP dediee, 2 boissons offertes, badge exclusif et experience premium garantie.',
            ],
            (object) [
                'question' => 'Mon billet QR est-il securise contre la fraude ?',
                'answer' => 'Oui. Chaque QR code est signe cryptographiquement et valide en temps reel a l\'entree. Impossible a dupliquer ou reutiliser.',
            ],
            (object) [
                'question' => 'Y a-t-il un age minimum pour acceder a l\'evenement ?',
                'answer' => 'L\'evenement est accessible a partir de 18 ans. Une piece d\'identite peut etre demandee a l\'entree.',
            ],
            (object) [
                'question' => 'Ou se deroule l\'evenement et comment y acceder ?',
                'answer' => 'Palais de la Culture, Abidjan. Acces facile en taxi, bus ou voiture. Un parking securise est disponible sur place.',
            ],
        ]);

        return view('public.home', compact('event', 'events', 'testimonials', 'faqItems'));
    }
}
