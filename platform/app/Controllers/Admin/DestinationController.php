<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Destination;
use App\Services\ActivityService;
use App\Helpers\Flash;

class DestinationController extends Controller
{
    /**
     * GET /admin/destinations
     *
     * List all destinations with edit/delete options.
     */
    public function index(): void
    {
        $db = Database::getInstance();

        $destinations = $db->query(
            "SELECT d.*,
                    (SELECT COUNT(*) FROM bookings WHERE destination_id = d.id) AS booking_count
             FROM destinations d
             ORDER BY d.name ASC"
        )->fetchAll();

        $this->view('admin.destinations.index', [
            'title'        => 'Destinations — Admin',
            'destinations' => $destinations,
        ]);
    }

    /**
     * POST /admin/destinations
     *
     * Create or update a destination.
     * If an "id" field is present in POST data, it updates; otherwise it creates.
     */
    public function store(): void
    {
        $id = (int) $this->request->post('id', 0);

        $errors = $this->validate([
            'name'    => 'required|max:255',
            'country' => 'required|max:100',
        ]);

        if (!empty($errors)) {
            Flash::errors($errors);
            Flash::old($this->request->allPost());
            $this->redirect('/admin/destinations');
            return;
        }

        $name       = trim($this->request->post('name', ''));
        $slug       = trim($this->request->post('slug', ''));
        $country    = trim($this->request->post('country', ''));
        $description = trim($this->request->post('description', ''));
        $priceFrom  = $this->request->post('price_from', null);
        $imageUrl   = trim($this->request->post('image_url', ''));
        $color      = trim($this->request->post('color', ''));
        $emoji      = trim($this->request->post('emoji', ''));
        $featured   = (int) $this->request->post('featured', 0);
        $status     = trim($this->request->post('status', 'active'));

        // Generate slug from name if not provided
        if ($slug === '') {
            $slug = $this->generateSlug($name);
        }

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'country'     => $country,
            'price_from'  => $priceFrom !== null && $priceFrom !== '' ? (float) $priceFrom : null,
            'image_url'   => $imageUrl,
            'color'       => $color,
            'emoji'       => $emoji,
            'featured'    => $featured ? 1 : 0,
            'status'      => in_array($status, ['active', 'inactive'], true) ? $status : 'active',
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            // Update existing
            $existing = Destination::find($id);
            if (!$existing) {
                Flash::error('Destination not found.');
                $this->redirect('/admin/destinations');
                return;
            }

            Destination::update($id, $data);
            ActivityService::log('destination.updated', 'destination', $id, ['name' => $name]);
            Flash::success('Destination updated successfully.');
        } else {
            // Create new
            $data['created_at'] = date('Y-m-d H:i:s');
            $newId = Destination::create($data);
            ActivityService::log('destination.created', 'destination', $newId, ['name' => $name]);
            Flash::success('Destination created successfully.');
        }

        $this->redirect('/admin/destinations');
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
