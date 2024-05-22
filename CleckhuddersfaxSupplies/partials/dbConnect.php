<?php

// Database class definition
class Database
{
    private $conn;

    public function __construct($username = 'hembikram', $password = 'Hem#123', $host = 'localhost', $dbname = 'xe')
    {
        $this->conn = oci_connect($username, $password, "//" . $host . "/" . $dbname);
        if (!$this->conn) {
            $m = oci_error();
            throw new Exception("Database connection failed: " . $m['message']);
        }
    }

    public function executeQuery($query)
    {
        $statement = oci_parse($this->conn, $query);
        if (!$statement) {
            $m = oci_error($this->conn);
            throw new Exception("Error preparing query: " . $m['message']);
        }
        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }
        return $statement;
    }

    public function fetchRow($statement)
    {
        return oci_fetch_assoc($statement);
    }

    public function closeConnection()
    {
        oci_close($this->conn);
    }

    public function getProducts()
    {
        $products = array();

        try {
            $query = "SELECT p.*, ROUND(r.average_rating, 2) AS rating
                      FROM product p
                      LEFT JOIN (
                          SELECT product_id, AVG(rating) AS average_rating
                          FROM review
                          GROUP BY product_id
                      ) r ON p.product_id = r.product_id";

            $statement = $this->executeQuery($query);

            while ($row = $this->fetchRow($statement)) {
                $products[] = $row;
            }

            $this->closeConnection();
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        return $products;
    }


    public function getProductImage($id) 
    {
        $query = 'SELECT PRODUCT_IMAGE FROM Product WHERE PRODUCT_ID = :id';
        $statement = oci_parse($this->conn, $query);
        oci_bind_by_name($statement, ":id", $id);
        oci_execute($statement);
        if (!$statement) {
            $m = oci_error($this->conn);
            throw new Exception("Error preparing query: " . $m['message']);
        }
        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }
        $row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_LOBS);

    
        if ($row) {
            $imageData = $row['PRODUCT_IMAGE'];
            $imageBase64 = base64_encode($imageData);
            return $imageBase64;
        } else {
            $imageBase64 = ''; 
        }
    }

    public function getProductById($productId) {
        $product = array();
        try {
            $query = "SELECT * FROM product WHERE PRODUCT_ID = :productId";
            $statement = oci_parse($this->conn, $query);
            oci_bind_by_name($statement, ":productId", $productId);
            oci_execute($statement);
    
            // Check if the query executed successfully
            if ($statement) {
                // Fetch the product data
                $product = oci_fetch_assoc($statement);
                oci_free_statement($statement);
            } else {
                // Handle the case where the query fails
                // For example, log the error or display an error message
                echo "Failed to execute the query.";
            }
        } catch (Exception $e) {
            // Handle any exceptions that may occur during query execution
            echo "Error: " . $e->getMessage();
        }
        return $product;
    }
    
    

    public function updateProduct($productId, $updatedData) {
        $setClause = [];
        $params = [];
    
        foreach ($updatedData as $column => $value) {
            $setClause[] = "$column = :$column";
            $params[":$column"] = $value;
        }
        
        $params[':productId'] = $productId;
        $setClause = implode(", ", $setClause);
        $query = "UPDATE product SET $setClause WHERE product_id = :productId";
        
        $statement = oci_parse($this->conn, $query);
        if (!$statement) {
            $m = oci_error($this->conn);
            throw new Exception("Error preparing query: " . $m['message']);
        }
        
        foreach ($params as $param => $value) {
            oci_bind_by_name($statement, $param, $params[$param]);
        }
        
        if (!oci_execute($statement)) {
            $m = oci_error($statement);
            throw new Exception("Error executing query: " . $m['message']);
        }
        
        return true;
    }
    
    


}

?>