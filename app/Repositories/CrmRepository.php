<?php
declare(strict_types=1);

namespace Healcare\Repositories;

use PDO;

final class CrmRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function allContacts(string $query = '', string $status = 'all'): array
    {
        $queryLower = mb_strtolower(trim($query), 'UTF-8');
        
        $sql = "SELECT * FROM crm_contacts WHERE 1=1";
        $params = [];
        
        if ($status !== 'all') {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }
        
        if ($queryLower !== '') {
            $sql .= " AND (LOWER(name) LIKE :query OR LOWER(phone) LIKE :query OR LOWER(message) LIKE :query OR LOWER(notes) LIKE :query)";
            $params['query'] = '%' . $queryLower . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createContact(array $data): array
    {
        $contact = [
            'id' => bin2hex(random_bytes(8)),
            'name' => trim((string) ($data['name'] ?? '')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
            'source' => (string) ($data['source'] ?? 'website'),
            'status' => 'new',
            'priority' => 'normal',
            'notes' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $sql = "INSERT INTO crm_contacts (id, name, phone, message, source, status, priority, notes, created_at, updated_at) 
                VALUES (:id, :name, :phone, :message, :source, :status, :priority, :notes, :created_at, :updated_at)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($contact);
        
        return $contact;
    }

    public function updateContact(string $id, string $status, string $priority, string $notes): bool
    {
        $allowedStatuses = ['new', 'in_progress', 'resolved', 'archived'];
        $allowedPriorities = ['low', 'normal', 'high'];
        if (!in_array($status, $allowedStatuses, true) || !in_array($priority, $allowedPriorities, true)) {
            return false;
        }

        $sql = "UPDATE crm_contacts SET status = :status, priority = :priority, notes = :notes, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $status,
            'priority' => $priority,
            'notes' => trim($notes),
            'updated_at' => date('Y-m-d H:i:s'),
            'id' => $id
        ]);
    }

    public function stats(): array
    {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM crm_contacts GROUP BY status");
        $results = $stmt->fetchAll();
        
        $stats = [
            'total' => 0,
            'new' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'archived' => 0,
        ];
        
        foreach ($results as $row) {
            $status = $row['status'];
            $count = (int)$row['count'];
            $stats['total'] += $count;
            if (isset($stats[$status])) {
                $stats[$status] = $count;
            }
        }
        
        return $stats;
    }
}
