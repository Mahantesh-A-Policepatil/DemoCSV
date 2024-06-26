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

        public function saveDataToParentTable()
        { 
            try {
                $voucherType = "DUE";
                $sql = "SELECT SUM(tc.due_amount) as amount, 
                tc.voucher_no, 
                tc.voucher_type, 
                tc.admno, b.id as br_id, 
                em.entry_mode_no, 
                em.crdr, 
                tc.academic_year
                FROM temporary_completedata tc
                JOIN entry_mode em ON tc.voucher_type = em.entry_mode_name
                JOIN branches b ON tc.faculty = b.branch_name
                WHERE tc.voucher_type = ?
                GROUP BY tc.admno";

                $stmt = $this->dbObj->dbconn->prepare($sql);
                $stmt->execute(array($voucherType));
                $financialTranResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo ($e->getMessage());
            }

            if (count($financialTranResult) > 0) {
                $tranDate = date('Y-m-d');
                while ($row = $financialTranResult) {
                    try {
                        $tranId = rand(100000, 999999);

                        $sqlQuery = "INSERT INTO financial_trans(
                        acad_year, 
                        tran_id, 
                        tran_date, 
                        br_id, 
                        voucher_no, 
                        entry_mode, 
                        admno, 
                        amount, 
                        crdr, 
                        type_of_concession
                    ) VALUES (?,?,?,?,?,?,?,?,?,?)";

                    $stmt = $this->dbObj->dbconn->prepare($sqlQuery);
                    $TypeOfConcession = 1;
                    $result = $stmt->execute(array(
                        $row['academic_year'], 
                        $tranId, 
                        $tranDate, 
                        $row['br_id'], 
                        $row['voucher_no'],
                        $row['entry_mode_no'], 
                        $row['admno'], 
                        $row['amount'], 
                        $row['crdr'], 
                        $TypeOfConcession)
                );
                } catch (PDOException $e) {
                    echo ($e->getMessage());
                }
                $this->insertDataToChildTable($row['voucher_no'], $tranId, $conn);
            }
        }
    }                    


    function insertDataToChildTable($voucherNo, $trandId, $conn)
    {
        try {
            $parentDataSql = "SELECT ft.tran_id, head.id as head_id, ft.br_id, ft.crdr, tc.fee_head, SUM(tc.due_amount) as amount
            FROM temporary_completedata1 tc
            JOIN financial_trans ft ON tc.voucher_no = ft.voucher_no
            JOIN fee_types head ON tc.fee_head = head.f_name and ft.br_id = head.br_id
            WHERE tc.voucher_type = 'DUE' and ft.voucher_no = ? and ft.tran_id = ?";

            $stmt = $this->dbObj->dbconn->prepare($sql);
            $stmt->execute(array($voucherNo, $trandId));
            $parentDataRes = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo ($e->getMessage());
        }

        if (count($parentDataRes) > 0) {

            while ($row = $parentDataRes->fetch_assoc()) {
                try {
                    $sql_insert = 'INSERT INTO financial_trans_detail(financial_tran_id, head_id, crdr, br_id, head_name, amount ) VALUES (?,?,?,?,?,?)';


                    $stmt = $this->dbObj->dbconn->prepare($sql_insert);
                    $result = $stmt->execute(array($row['tran_id'], $row['head_id'], $row['crdr'], $row['br_id'], $row['fee_head'], $row['amount']));
                } catch (PDOException $e) {
                    echo ($e->getMessage());
                }
            }

        }
    }
