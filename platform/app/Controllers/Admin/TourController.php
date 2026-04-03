<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Tour;
use App\Models\Destination;
use App\Services\ActivityService;
use App\Helpers\Flash;

class TourController extends Controller
{
    /**
     * GET /admin/tours
     *
     * List all tours with optional type filter.
     */
    public function index(): void
    {
        $typeFilter = $this->request->get('type', '');

        $db = Database::getInstance();

        if ($typeFilter && in_array($typeFilter, ['ingoing', 'outgoing', 'transfer'], true)) {
            $tours = $db->query(
                "SELECT t.*, d.name AS destination_name
                 FROM tours t
                 LEFT JOIN destinations d ON t.destination_id = d.id
                 WHERE t.type = ?
                 ORDER BY t.featured DESC, t.created_at DESC",
                [$typeFilter]
            )->fetchAll();
        } else {
            $tours = $db->query(
                "SELECT t.*, d.name AS destination_name
                 FROM tours t
                 LEFT JOIN destinations d ON t.destination_id = d.id
                 ORDER BY t.type ASC, t.featured DESC, t.created_at DESC"
            )->fetchAll();
        }

        $destinations = Destination::active();

        $this->view('admin.tours', [
            'title'        => 'Tours — Admin',
            'tours'        => $tours,
            'destinations' => $destinations,
            'typeFilter'   => $typeFilter,
        ]);
    }

    /**
     * POST /admin/tours
     *
     * Create or update a tour.
     * If an "id" field is present in POST data, it updates; otherwise it creates.
     */
    public function store(): void
    {
        $id = (int) $this->request->post('id', 0);

        $errors = $this->validate([
            'title' => 'required|max:255',
            'type'  => 'required',
        ]);

        if (!empty($errors)) {
            Flash::errors($errors);
            Flash::old($this->request->allPost());
            $this->redirect('/admin/tours');
            return;
        }

        $title         = trim($this->request->post('title', ''));
        $slug          = trim($this->request->post('slug', ''));
        $type          = trim($this->request->post('type', 'ingoing'));
        $description   = trim($this->request->post('description', ''));
        $duration      = trim($this->request->post('duration', ''));
        $priceFrom     = $this->request->post('price_from', null);
        $imageUrl      = trim($this->request->post('image_url', ''));
        $destinationId = $this->request->post('destination_id', null);
        $featured      = (int) $this->request->post('featured', 0);
        $status        = trim($this->request->post('status', 'active'));

        // Build itinerary JSON from day inputs
        $itinerary = [];
        $dayTexts = $this->request->post('itinerary_day', []);
        if (is_array($dayTexts)) {
            foreach ($dayTexts as $i => $text) {
                $text = trim($text);
                if ($text !== '') {
                    $itinerary[] = [
                        'day'         => $i + 1,
                        'description' => $text,
                    ];
                }
            }
        }

        // Generate slug from title if not provided
        if ($slug === '') {
            $slug = $this->generateSlug($title);
        }

        // Validate type
        if (!in_array($type, ['ingoing', 'outgoing', 'transfer'], true)) {
            $type = 'ingoing';
        }

        $data = [
            'title'          => $title,
            'slug'           => $slug,
            'type'           => $type,
            'description'    => $description,
            'itinerary'      => !empty($itinerary) ? json_encode($itinerary) : null,
            'duration'       => $duration ?: null,
            'price_from'     => $priceFrom !== null && $priceFrom !== '' ? (float) $priceFrom : null,
            'image_url'      => $imageUrl,
            'destination_id' => $destinationId !== null && $destinationId !== '' ? (int) $destinationId : null,
            'featured'       => $featured ? 1 : 0,
            'status'         => in_array($status, ['active', 'inactive'], true) ? $status : 'active',
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            // Update existing
            $existing = Tour::find($id);
            if (!$existing) {
                Flash::error('Tour not found.');
                $this->redirect('/admin/tours');
                return;
            }

            Tour::update($id, $data);
            ActivityService::log('tour.updated', 'tour', $id, ['title' => $title]);
            Flash::success('Tour updated successfully.');
        } else {
            // Create new
            $data['created_at'] = date('Y-m-d H:i:s');
            $newId = Tour::create($data);
            ActivityService::log('tour.created', 'tour', $newId, ['title' => $title]);
            Flash::success('Tour created successfully.');
        }

        $this->redirect('/admin/tours');
    }

    /**
     * POST /admin/tours/delete
     *
     * Delete a tour by ID.
     */
    public function delete(): void
    {
        $id = (int) $this->request->post('id', 0);

        if ($id <= 0) {
            Flash::error('Invalid tour ID.');
            $this->redirect('/admin/tours');
            return;
        }

        $tour = Tour::find($id);
        if (!$tour) {
            Flash::error('Tour not found.');
            $this->redirect('/admin/tours');
            return;
        }

        Tour::delete($id);
        ActivityService::log('tour.deleted', 'tour', $id, ['title' => $tour['title']]);
        Flash::success('Tour "' . $tour['title'] . '" deleted.');

        $this->redirect('/admin/tours');
    }

    /**
     * Generate a URL-friendly slug from a string.
     */
    private function generateSlug(string $text): string
    {
        $slug = mb_strtolower($text, 'UTF-8');
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
}
