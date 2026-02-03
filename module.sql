-- ============================================================
-- SARS Representative Registration Module - Database Setup
-- Run this SQL in your database before using the module
-- ============================================================

-- 1. SARS Representatives (Person profiles - reusable across clients)
CREATE TABLE IF NOT EXISTS sars_representatives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    id_number VARCHAR(20) NULL,
    passport_number VARCHAR(50) NULL,
    email VARCHAR(150) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    capacity ENUM('director','sole_director','trustee','sole_trustee','accounting_officer','chairperson','treasurer','accountant','authorised_representative') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY idx_id_number (id_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. SARS Representative Requests (Master workflow record)
CREATE TABLE IF NOT EXISTS sars_rep_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Entity details (standalone - no FK to client_master)
    entity_name VARCHAR(255) NOT NULL,
    entity_reg_number VARCHAR(50) NULL,
    entity_type ENUM('company','trust','npc','npo','sole_director_company','sole_trustee_trust') NOT NULL,
    income_tax_ref VARCHAR(50) NULL,
    paye_ref VARCHAR(50) NULL,
    vat_ref VARCHAR(50) NULL,
    uif_sdl_ref VARCHAR(50) NULL,
    entity_address TEXT NULL,
    -- Representative link
    sars_representative_id INT NOT NULL,
    -- Tax types (stored as JSON for flexibility)
    tax_types JSON NULL,
    -- Submission details
    submission_method ENUM('branch','efiling','both') NOT NULL DEFAULT 'both',
    status ENUM('draft','awaiting_documents','ready_for_review','ready_for_submission','submitted_branch','submitted_efiling','approved','rejected') NOT NULL DEFAULT 'draft',
    rejection_reason TEXT NULL,
    submitted_at DATETIME NULL,
    approved_at DATETIME NULL,
    notes TEXT NULL,
    created_by INT NULL,
    assigned_to INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_entity_name (entity_name),
    INDEX idx_sars_rep_id (sars_representative_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. SARS Representative Documents (Uploaded & generated)
CREATE TABLE IF NOT EXISTS sars_rep_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sars_rep_request_id INT NOT NULL,
    document_type ENUM('cover_letter','sars_mandate','resolution','entity_registration','representative_id','representative_address','representative_photo','trust_deed','letters_of_authority','npo_certificate','supporting_document','generated_pdf_bundle') NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    original_filename VARCHAR(255) NULL,
    stored_filename VARCHAR(255) NULL,
    file_size INT NULL,
    mime_type VARCHAR(100) NULL,
    status ENUM('uploaded','generated','approved','rejected') NOT NULL DEFAULT 'uploaded',
    expiry_date DATE NULL,
    notes TEXT NULL,
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_request_id (sars_rep_request_id),
    INDEX idx_document_type (document_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. SARS Representative Audit Logs
CREATE TABLE IF NOT EXISTS sars_rep_audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sars_rep_request_id INT NOT NULL,
    performed_by INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_request_id (sars_rep_request_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. SARS Representative Status History
CREATE TABLE IF NOT EXISTS sars_rep_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sars_rep_request_id INT NOT NULL,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50) NOT NULL,
    changed_by INT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_request_id (sars_rep_request_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
