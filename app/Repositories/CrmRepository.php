<?php
declare(strict_types=1);

namespace Healcare\Repositories;

final class CrmRepository
{
    private string $storageFile;

    public function __construct(?string $storageFile = null)
    {
        $this->storageFile = $storageFile ?: __DIR__ . '/../../data/storage/crm_contacts.php';
    }

    public function allContacts(string $query = '', string $status = 'all'): array
    {
        $contacts = $this->read();
        $query = mb_strtolower(trim($query), 'UTF-8');

        $contacts = array_filter($contacts, static function (array $contact) use ($query, $status): bool {
            if ($status !== 'all' && ($contact['status'] ?? '') !== $status) {
                return false;
            }
            if ($query === '') {
                return true;
            }
            $haystack = mb_strtolower(implode(' ', [
                (string) ($contact['name'] ?? ''),
                (string) ($contact['phone'] ?? ''),
                (string) ($contact['message'] ?? ''),
                (string) ($contact['notes'] ?? ''),
            ]), 'UTF-8');
            return mb_strpos($haystack, $query) !== false;
        });

        usort($contacts, static fn(array $left, array $right): int => strcmp(
            (string) ($right['created_at'] ?? ''),
            (string) ($left['created_at'] ?? '')
        ));

        return array_values($contacts);
    }

    public function createContact(array $data): array
    {
        $contacts = $this->read();
        $contact = [
            'id' => bin2hex(random_bytes(8)),
            'name' => trim((string) ($data['name'] ?? '')),
            'phone' => trim((string) ($data['phone'] ?? '')),
            'message' => trim((string) ($data['message'] ?? '')),
            'source' => (string) ($data['source'] ?? 'website'),
            'status' => 'new',
            'priority' => 'normal',
            'notes' => '',
            'created_at' => date(DATE_ATOM),
            'updated_at' => date(DATE_ATOM),
        ];
        $contacts[] = $contact;
        $this->write($contacts);
        return $contact;
    }

    public function updateContact(string $id, string $status, string $priority, string $notes): bool
    {
        $allowedStatuses = ['new', 'in_progress', 'resolved', 'archived'];
        $allowedPriorities = ['low', 'normal', 'high'];
        if (!in_array($status, $allowedStatuses, true) || !in_array($priority, $allowedPriorities, true)) {
            return false;
        }

        $contacts = $this->read();
        foreach ($contacts as &$contact) {
            if (($contact['id'] ?? '') !== $id) {
                continue;
            }
            $contact['status'] = $status;
            $contact['priority'] = $priority;
            $contact['notes'] = trim($notes);
            $contact['updated_at'] = date(DATE_ATOM);
            $this->write($contacts);
            return true;
        }
        unset($contact);
        return false;
    }

    public function stats(): array
    {
        $contacts = $this->read();
        $stats = [
            'total' => count($contacts),
            'new' => 0,
            'in_progress' => 0,
            'resolved' => 0,
            'archived' => 0,
        ];
        foreach ($contacts as $contact) {
            $status = (string) ($contact['status'] ?? 'new');
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }
        return $stats;
    }

    private function read(): array
    {
        if (!is_file($this->storageFile)) {
            return [];
        }
        $data = require $this->storageFile;
        return is_array($data) ? array_values(array_filter($data, 'is_array')) : [];
    }

    private function write(array $contacts): void
    {
        $directory = dirname($this->storageFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents(
            $this->storageFile,
            "<?php\n\nreturn " . var_export(array_values($contacts), true) . ";\n",
            LOCK_EX
        );
    }
}
