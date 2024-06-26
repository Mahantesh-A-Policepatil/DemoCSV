<?php

include_once "../../Config/DBManager.php";

class DataController
{
    private $dbObj;

    /**
     * Method __construct
     *
     * @return void
     */
    function __construct()
    {
        $this->dbObj = new DBManager();
    }

    /**
     * Method store
     *
     *
     * @return void
     */
    public function store()
    {
        try {
            // Check if file is uploaded successfully
            if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload error");
            }

            // Validate file type
            $fileType = $_FILES["file"]["type"];
            if (
                $fileType !== "text/csv" &&
                $fileType !== "application/vnd.ms-excel"
            ) {
                throw new Exception(
                    "Invalid file type. Only CSV files are allowed."
                );
            }

            // Move uploaded file to a temporary location
            $tmpFilePath = $_FILES["file"]["tmp_name"];
            $csv = fopen($tmpFilePath, "r");

            if ($csv === false) {
                throw new Exception("Error opening uploaded CSV file");
            }

            // Begin transaction
            $this->dbObj->dbconn->beginTransaction();
            $headers = [
                "id",
                "date",
                "academic_year",
                "session",
                "alloted_category",
                "voucher_type",
                "voucher_no",
                "roll_no",
                "admno",
                "status",
                "fee_category",
                "faculty",
                "program",
                "department",
                "batch",
                "receipt_no",
                "fee_head",
                "due_amount",
                "paid_amount",
                "concession_amount",
                "scholarship_amount",
                "reverse_concession_amount",
                "write_off_amount",
                "adjusted_amount",
                "refund_amount",
                "fund_trancfer_amount",
                "remarks",
            ];
            $header_row = implode(",", $headers);
            $stmt = $this->dbObj->dbconn->prepare(
                "INSERT INTO temporary_completedata (" . $header_row . ")
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            for ($i = 0; $i <= 5; $i++) {
                fgetcsv($csv); // Read and discard each line
            }

            // Read CSV file and insert into database
            while (($row = fgetcsv($csv)) !== false) {
                $row[1] = date("d/m/Y", strtotime($row[1]));
                $stmt->execute($row);
            }

            // Commit transaction
            $this->dbObj->dbconn->commit();
            fclose($csv);
            //$pdo = null; // Close database connection

            echo "CSV imported successfully";
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
