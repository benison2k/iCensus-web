<?php
// app/models/Residents.php

class Resident {
    private $pdo;

    // --- SECURITY: Whitelist of allowed fields for Mass Assignment Protection ---
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'suffix', 'alias',
        'dob', 'gender', 'civil_status', 
        'nationality', 'residency_status', 'religion',
        'blood_type',
        'house_no', 'street', 'purok', 'household_no', 
        'head_of_household', 'relationship', 'ownership_status',
        'occupation', 'monthly_income', 'educational_attainment',
        'contact_number', 'email', 
        'emergency_name', 'emergency_contact',
        'is_pwd', 'is_solo_parent', 'is_4ps_member',
        'voter_status', 'precinct_no', 
        'national_id_no', 'philhealth_no', 'sss_no', 'tin_no',
        'encoded_by' // Allowed: Controller sets this from the session
    ];

    public function __construct(Database $db) {
        $this->pdo = $db->getPdo();
    }

    /**
     * Generic function to get distinct, non-empty values from a column.
     * Includes security check to prevent SQL injection via column name.
     */
    public function getDistinctValues($column) {
        // Validation: Whitelist allowed columns for dynamic selection
        $allowed_columns = [
            'civil_status', 'blood_type', 'nationality', 'residency_status', 
            'relationship', 'educational_attainment', 'occupation', 'ownership_status',
            'street', 'purok', 'gender'
        ];
        
        if (!in_array($column, $allowed_columns)) {
            // Return empty if invalid column is requested (Security Fail-safe)
            return [];
        }

        $stmt = $this->pdo->prepare("
            SELECT DISTINCT {$column} 
            FROM residents 
            WHERE {$column} IS NOT NULL AND {$column} != '' 
            ORDER BY {$column} ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getHouseholdHeads() {
        $stmt = $this->pdo->query("
            SELECT DISTINCT head_of_household 
            FROM residents 
            WHERE head_of_household IS NOT NULL AND head_of_household != ''
            ORDER BY head_of_household ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function findByAddress($house_no, $street, $purok) {
        $stmt = $this->pdo->prepare("
            SELECT id, first_name, last_name, relationship 
            FROM residents 
            WHERE house_no = ? AND street = ? AND purok = ?
        ");
        $stmt->execute([$house_no, $street, $purok]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchHeads($term) {
        $stmt = $this->pdo->prepare("
            SELECT CONCAT(first_name, ' ', last_name) as name 
            FROM residents 
            WHERE (relationship = 'Self' OR relationship = '')
            AND CONCAT(first_name, ' ', last_name) LIKE ?
            LIMIT 10
        ");
        $stmt->execute(['%' . $term . '%']);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Fetches all APPROVED residents from the database for the main view.
     */
    public function getAll() {
        $sql = "SELECT *, TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age 
                FROM residents 
                WHERE approval_status = 'approved' 
                ORDER BY last_name ASC, first_name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all residents awaiting approval.
     */
    public function getPending() {
        $sql = "SELECT *, TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age 
                FROM residents 
                WHERE approval_status = 'pending' 
                ORDER BY created_at ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPendingPaginated($page = 1, $pageSize = 10) {
        $offset = ($page - 1) * $pageSize;

        $countStmt = $this->pdo->query("SELECT COUNT(*) FROM residents WHERE approval_status = 'pending'");
        $total = $countStmt->fetchColumn();

        $stmt = $this->pdo->prepare("
            SELECT *, TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age 
            FROM residents 
            WHERE approval_status = 'pending' 
            ORDER BY created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', (int) $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'residents' => $residents,
            'total' => $total,
            'totalPages' => ceil($total / $pageSize)
        ];
    }

    public function getPendingCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM residents WHERE approval_status = 'pending'");
        return $stmt->fetchColumn();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM residents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAnyStatus($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM residents WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findDuplicate($firstName, $lastName, $dob, $excludeId = null) {
        $sql = "SELECT id, first_name, last_name, dob, approval_status 
                FROM residents 
                WHERE first_name = ? AND last_name = ? AND dob = ?";
        $params = [$firstName, $lastName, $dob];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * SECURE SAVE METHOD (Fixes Mass Assignment Vulnerability)
     */
    public function save($data) {
        // 1. FILTER: Only allow keys that are in the $fillable whitelist
        $safeData = array_intersect_key($data, array_flip($this->fillable));

        // 2. SANITIZE: Convert empty strings to NULL for database consistency
        foreach ($safeData as $key => $value) {
            if ($value === '') $safeData[$key] = null;
        }

        if (empty($data['resident_id'])) {
            // --- CREATE MODE ---
            
            // Set system fields that are NOT user-editable
            $safeData['date_added'] = date('Y-m-d H:i:s');
            $safeData['created_at'] = date('Y-m-d H:i:s');
            $safeData['approval_status'] = 'pending'; // Always pending on creation
            
            // Ensure encoded_by is captured if provided by Controller
            if(!isset($safeData['encoded_by']) && isset($data['encoded_by'])) {
                 $safeData['encoded_by'] = $data['encoded_by'];
            }
            
            $fields = array_keys($safeData);
            $placeholders = array_fill(0, count($fields), '?');
            
            $stmt = $this->pdo->prepare("INSERT INTO residents (" . implode(",", $fields) . ") VALUES (" . implode(",", $placeholders) . ")");
            $stmt->execute(array_values($safeData));
            return $this->pdo->lastInsertId();

        } else {
            // --- UPDATE MODE ---
            $id = $data['resident_id'];
            
            // Set update timestamp
            $safeData['last_updated'] = date('Y-m-d H:i:s');
            
            // Protect immutable fields during update
            unset($safeData['created_at']);
            unset($safeData['date_added']);
            unset($safeData['encoded_by']); 
            unset($safeData['approval_status']); // Prevent self-approval via edit
            
            $setStr = implode(',', array_map(fn($f) => "$f=?", array_keys($safeData)));
            
            $stmt = $this->pdo->prepare("UPDATE residents SET $setStr WHERE id = ?");
            $values = array_values($safeData);
            $values[] = $id;
            $stmt->execute($values);
            return $id;
        }
    }

    public function approve($id, $adminId) {
        $stmt = $this->pdo->prepare("UPDATE residents SET approval_status = 'approved', approved_by = ?, date_approved = NOW() WHERE id = ?");
        return $stmt->execute([$adminId, $id]);
    }

    public function approveAll($adminId) {
        $stmt = $this->pdo->prepare(
            "UPDATE residents SET approval_status = 'approved', approved_by = ?, date_approved = NOW() WHERE approval_status = 'pending'"
        );
        $stmt->execute([$adminId]);
        return $stmt->rowCount();
    }

    public function reject($id) {
        $stmt = $this->pdo->prepare("DELETE FROM residents WHERE id = ? AND approval_status = 'pending'");
        return $stmt->execute([$id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM residents WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getFiltered($filters) {
        $query = "SELECT *, TIMESTAMPDIFF(YEAR, dob, CURDATE()) as age FROM residents WHERE approval_status = 'approved'";
        $params = [];

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query .= " AND DATE(date_approved) BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['gender'])) {
            $query .= " AND gender = ?";
            $params[] = $filters['gender'];
        }
        if (!empty($filters['civil_status'])) {
            $query .= " AND civil_status = ?";
            $params[] = $filters['civil_status'];
        }
        if (!empty($filters['purok'])) {
            $query .= " AND purok = ?";
            $params[] = $filters['purok'];
        }
        if (!empty($filters['blood_type'])) {
            $query .= " AND blood_type = ?";
            $params[] = $filters['blood_type'];
        }
        if (!empty($filters['educational_attainment'])) {
            $query .= " AND educational_attainment = ?";
            $params[] = $filters['educational_attainment'];
        }
        if (!empty($filters['occupation'])) {
            $query .= " AND occupation = ?";
            $params[] = $filters['occupation'];
        }
        if (isset($filters['employment_status']) && $filters['employment_status'] !== '') {
            if ($filters['employment_status'] === 'employed') {
                $query .= " AND (occupation IS NOT NULL AND occupation != '' AND LOWER(occupation) NOT IN ('unemployed', 'n/a', 'student'))";
            } else {
                $query .= " AND (occupation IS NULL OR occupation = '' OR LOWER(occupation) IN ('unemployed', 'n/a', 'student'))";
            }
        }
        if (!empty($filters['is_pwd'])) {
            $query .= " AND is_pwd = ?";
            $params[] = $filters['is_pwd'];
        }
        if (!empty($filters['is_solo_parent'])) {
            $query .= " AND is_solo_parent = ?";
            $params[] = $filters['is_solo_parent'];
        }
        if (!empty($filters['is_4ps_member'])) {
            $query .= " AND is_4ps_member = ?";
            $params[] = $filters['is_4ps_member'];
        }
        if (!empty($filters['age_min'])) {
            $query .= " AND TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= ?";
            $params[] = $filters['age_min'];
        }
        if (!empty($filters['age_max'])) {
            $query .= " AND TIMESTAMPDIFF(YEAR, dob, CURDATE()) <= ?";
            $params[] = $filters['age_max'];
        }
        if (!empty($filters['relationship'])) {
            $query .= " AND relationship = ?";
            $params[] = $filters['relationship'];
        }

        if (!empty($filters['generation'])) {
            $generation = $filters['generation'];
            $yearCondition = '';
            switch ($generation) {
                case 'Gen Alpha': $yearCondition = "YEAR(dob) >= 2013"; break;
                case 'Gen Z': $yearCondition = "YEAR(dob) BETWEEN 1997 AND 2012"; break;
                case 'Millennials': $yearCondition = "YEAR(dob) BETWEEN 1981 AND 1996"; break;
                case 'Gen X': $yearCondition = "YEAR(dob) BETWEEN 1965 AND 1980"; break;
                case 'Baby Boomers': $yearCondition = "YEAR(dob) BETWEEN 1946 AND 1964"; break;
                case 'Older': $yearCondition = "YEAR(dob) < 1946"; break;
                case 'Unknown': $yearCondition = "(dob IS NULL OR dob = '0000-00-00')"; break;
            }
            if ($yearCondition) {
                $query .= " AND ($yearCondition)";
            }
        }
        
        if (!empty($filters['is_head']) && $filters['is_head'] === 'Yes') {
            $query .= " AND relationship = 'Self'";
        }
        
        if (!empty($filters['street'])) {
            $query .= " AND street = ?";
            $params[] = $filters['street'];
        }

        if (!empty($filters['has_field'])) {
            $allowed_fields = ['contact_number', 'email', 'emergency_name', 'blood_type'];
            $field = $filters['has_field'];
            if (in_array($field, $allowed_fields)) {
                $query .= " AND ({$field} IS NOT NULL AND {$field} != '')";
            }
        }

        if (!empty($filters['household_size'])) {
            $size = intval($filters['household_size']);
            $operator = str_contains($filters['household_size'], '+') ? '>=' : '=';
            $query .= " AND household_no IN (SELECT household_no FROM residents WHERE household_no IS NOT NULL AND household_no != '' GROUP BY household_no HAVING COUNT(*) {$operator} ?)";
            $params[] = $size;
        } 
        $query .= " ORDER BY last_name, first_name";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatsForEncoder($encoderId) {
        $stats = [];

        // Entries Submitted Today
        $stmt_today = $this->pdo->prepare("SELECT COUNT(*) FROM residents WHERE encoded_by = ? AND DATE(created_at) = CURDATE()");
        $stmt_today->execute([$encoderId]);
        $stats['today'] = $stmt_today->fetchColumn();

        // Entries Pending Approval
        $stmt_pending = $this->pdo->prepare("SELECT COUNT(*) FROM residents WHERE encoded_by = ? AND approval_status = 'pending'");
        $stmt_pending->execute([$encoderId]);
        $stats['pending'] = $stmt_pending->fetchColumn();

        // Total Entries Approved
        $stmt_approved = $this->pdo->prepare("SELECT COUNT(*) FROM residents WHERE encoded_by = ? AND approval_status = 'approved'");
        $stmt_approved->execute([$encoderId]);
        $stats['approved'] = $stmt_approved->fetchColumn();

        return $stats;
    }
    
    public function getRecentByEncoder($encoderId, $limit = 5) {
        $sql = "SELECT first_name, last_name, created_at, approval_status 
                FROM residents 
                WHERE encoded_by = :encoder_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':encoder_id', $encoderId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}