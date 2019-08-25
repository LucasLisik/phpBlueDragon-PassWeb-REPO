<?php

/**
 * @author:    Lukasz Sosna
 * @e-mail:    lukasz.bluedragon@gmail.com
 * @www:       http://phpbluedragon.pl
 * @copyright: 8-7-2015 12:34
 *
 */

class System_model extends CI_Model 
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function DeleteOldPasswords()
    {
        //DELETE FROM locks WHERE time_created < (UNIX_TIMESTAMP() - 600);
        // 3600 - one hour
        
        $QueryResult = $this->db->query('UPDATE 
		{PREFIXDB}category
        SET
        category_name_old = "",
        category_change = "0000-00-00 00:00:00"
        WHERE
        category_change < (NOW() - INTERVAL +1 HOUR)
        AND
        category_change != "0000-00-00 00:00:00"
		');
        
        $QueryResult = $this->db->query('UPDATE 
		{PREFIXDB}passwordlist
        SET
        passwordlist_password_old = "",
        passwordlist_iv_old = "",
        passwordlist_change = "0000-00-00 00:00:00"
        WHERE
        passwordlist_change < (NOW() - INTERVAL +1 HOUR)
        AND
        passwordlist_change != "0000-00-00 00:00:00"
		');
        
        $QueryResult = $this->db->query('UPDATE 
		{PREFIXDB}passwords
        SET
        pass_title_old = "",
        pass_user_old = "",
        pass_password_old = "",
        pass_url_old = "",
        pass_note_old = "",
        pass_change = "0000-00-00 00:00:00"
        WHERE
        pass_change < (NOW() - INTERVAL +1 HOUR)
        AND
        pass_change != "0000-00-00 00:00:00"
		');
    }
    
    public function GetPasswordsList()
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwordlist
        ORDER BY
        passwordlist_name ASC
		');
        		
		return $QueryResult;
    }
    
    public function CheckUser($ListId,$PassList)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwordlist
        WHERE
        passwordlist_id = "'.$this->db->escape_str($ListId).'"
		');
        
        foreach($QueryResult->result() as $row)
		{
            $PasswordToVerify = $row->passwordlist_password;
            $IVToVerify = $row->passwordlist_iv;
            $UserId = $row->passwordlist_id;
            $PassIV = $row->passwordlist_iv;
        }
        
        if($PassList != $this->decrypt($PasswordToVerify,$IVToVerify,$PassList))
        {
            $TableUser['IsAuth'] = 'no';
        }
        else
        {
            $TableUser['IsAuth'] = 'yes';
            $TableUser['UserId'] = $UserId;
            $TableUser['UserPassword'] = $PassList;
            $TableUser['UserIV'] = $PassIV;
        }
        
        return $TableUser;
    }
    
    public function decrypt($String, $IV, $Password)
    {
        $String = base64_decode($String);
        $IV = base64_decode($IV);
        
        $decryptedString = openssl_decrypt($String, "aes-256-cbc", $Password, OPENSSL_RAW_DATA, $IV);
        
        return $decryptedString;
    }
    
    public function encrypt($String, $IV, $Password)
    {
        $GeneratedArray = null;
        
        $encryptedString = openssl_encrypt($String,"aes-256-cbc", $Password, OPENSSL_RAW_DATA, $IV);

        $GeneratedArray['password'] = base64_encode($encryptedString);
        
        $GeneratedArray['iv'] = base64_encode($IV);
        
        return $GeneratedArray;
    }
    
    public function CreateNewRepo($RepoName,$RepoPass)
    {       
        $IvLength = openssl_cipher_iv_length('aes-256-cbc');
        $ivReady = openssl_random_pseudo_bytes($IvLength);
        
        $GeneratedArray = $this->encrypt($RepoPass,$ivReady,$RepoPass);
        
        $RepoName = htmlspecialchars($RepoName);
        
        $QueryResult = $this->db->query('INSERT INTO 
        {PREFIXDB}passwordlist
        (
        passwordlist_name,
        passwordlist_password,
        passwordlist_iv
        )
        VALUES
        (
        "'.$this->db->escape_str($RepoName).'",
        "'.$this->db->escape_str($GeneratedArray['password']).'",
        "'.$this->db->escape_str($GeneratedArray['iv']).'"
        )
        ');
    }

    public function SelectCategoryList($UserId)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_password_id = "'.$this->db->escape_str($UserId).'"
        ORDER BY
        category_name ASC
		');
        		
		return $QueryResult;
    }
    
    public function SelectOnlyMainCategoryList($UserId)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_password_id = "'.$this->db->escape_str($UserId).'"
        AND 
        category_password_sub = "0"
        ORDER BY
        category_name ASC
		');
        		
		return $QueryResult;
    }
    
    /*public function SelectCategoryIV($CategoryId)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
        {PREFIXDB}category');
        		
		return $QueryResult;
    }*/
    
    public function SelectPasswordList($UserId,$SelectedCateroryId)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwords
        WHERE
        pass_password_id = "'.$this->db->escape_str($UserId).'"
        AND
        pass_category_id = "'.$this->db->escape_str($SelectedCateroryId).'"
        ORDER BY
        pass_id ASC
		');
        		
		return $QueryResult;
    }
    
    public function CreateNewCategory($CategoryName)
    {       
        $GeneratedArray = $this->encrypt($CategoryName,base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);

        //"'.$this->db->escape_str($CategoryName).'",
        
        $QueryResult = $this->db->query('INSERT INTO 
        {PREFIXDB}category
        (
        category_name,
        category_password_id
        )
        VALUES
        (
        "'.$this->db->escape_str($GeneratedArray['password']).'",
        "'.$this->db->escape_str($_SESSION['user_id']).'"
        )
        ');
    }
    
    public function CreateNewSubCategory($MainCatgory,$CategoryName)
    {
        $GeneratedArray = $this->encrypt($CategoryName,base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        
        $QueryResult = $this->db->query('INSERT INTO 
        {PREFIXDB}category
        (
        category_name,
        category_password_id,
        category_password_sub
        )
        VALUES
        (
        "'.$this->db->escape_str($GeneratedArray['password']).'",
        "'.$this->db->escape_str($_SESSION['user_id']).'",
        "'.$this->db->escape_str($MainCatgory).'"
        )
        ');
    }
    
    public function EditCategory($CategoryName,$CategoryId)
    {
        $GeneratedArray = $this->encrypt($CategoryName,base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        
        $QueryResult = $this->db->query('UPDATE 
        {PREFIXDB}category
        SET
        category_name = "'.$this->db->escape_str($GeneratedArray['password']).'"
        WHERE
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
        AND
        category_id = "'.$this->db->escape_str($CategoryId).'"
        ');
    }
    
    public function EditSubCategory($MainCatgory,$CategoryName,$CategoryId)
    {
        $GeneratedArray = $this->encrypt($CategoryName,base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        
        $QueryResult = $this->db->query('UPDATE 
        {PREFIXDB}category
        SET
        category_name = "'.$this->db->escape_str($GeneratedArray['password']).'",
        category_password_sub = "'.$this->db->escape_str($MainCatgory).'"
        WHERE
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
        AND
        category_id = "'.$this->db->escape_str($CategoryId).'"
        ');
    }
    
    public function SelectOneCategory($CategoryId)
    {
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_id = "'.$this->db->escape_str($CategoryId).'"
        AND
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
                        
		return $QueryResult;
    }
    
    public function DeleteCategory($CategoryId)
    {
        // Usuwanie kategorii
        $QueryResult = $this->db->query('DELETE FROM 
		{PREFIXDB}category
        WHERE
        category_id = "'.$this->db->escape_str($CategoryId).'"
        AND
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        // Usuwanie haseł z kategorii
        $QueryResult = $this->db->query('DELETE FROM 
		{PREFIXDB}passwords
        WHERE
        pass_category_id = "'.$this->db->escape_str($CategoryId).'"
        AND
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        // Wybieranie podkategorii
        $QueryResultPass = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_password_sub = "'.$this->db->escape_str($CategoryId).'"
        AND
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        		        
        // Usuwanie podkategorii
        foreach($QueryResultPass->result() as $row)
        {
            $QueryResult = $this->db->query('DELETE FROM 
    		{PREFIXDB}category
            WHERE
            category_id = "'.$this->db->escape_str($row->category_id).'"
            AND
            category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    		');
        }
        
        // Usuwanie haseł z podkategorii
        foreach($QueryResultPass->result() as $row)
        {
            $QueryResult = $this->db->query('DELETE FROM 
    		{PREFIXDB}passwords
            WHERE
            pass_category_id = "'.$this->db->escape_str($row->category_id).'"
            AND
            pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    		');
        }
    }
    
    public function CreateNewPassword($CategoryId)
    {        
        $GeneratedArrayTitle = $this->encrypt($this->input->post('pass_title'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayUser = $this->encrypt($this->input->post('pass_user'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayPassword = $this->encrypt($this->input->post('pass_password'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayUrl = $this->encrypt($this->input->post('pass_url'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayNote = $this->encrypt(nl2br($this->input->post('pass_note')),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        
        //category_name = "'.$this->db->escape_str($GeneratedArray['password']).'",
        
        $QueryResult = $this->db->query('INSERT INTO 
        {PREFIXDB}passwords
        (
        pass_title,
        pass_user,
        pass_password,
        pass_url,
        pass_note,
        pass_date_added,
        pass_category_id,
        pass_password_id
        )
        VALUES
        (
        "'.$this->db->escape_str($GeneratedArrayTitle['password']).'",
        "'.$this->db->escape_str($GeneratedArrayUser['password']).'",
        "'.$this->db->escape_str($GeneratedArrayPassword['password']).'",
        "'.$this->db->escape_str($GeneratedArrayUrl['password']).'",
        "'.$this->db->escape_str($GeneratedArrayNote['password']).'",
        "'.$this->db->escape_str(date('Y-m-d H:i:s')).'",
        "'.$this->db->escape_str($CategoryId).'",
        "'.$this->db->escape_str($_SESSION['user_id']).'"
        )
        ');
    }
    
    public function DeletePassword($PasswordId)
    {
        $QueryResult = $this->db->query('DELETE FROM 
  		{PREFIXDB}passwords
        WHERE
        pass_id = "'.$this->db->escape_str($PasswordId).'"
        AND
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
  		');
    }
    
    public function SelectOnePassword($PasswordId)
    {
        $QueryResultPass = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwords
        WHERE
        pass_id = "'.$this->db->escape_str($PasswordId).'"
        AND
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        return $QueryResultPass;
    }
    
    public function EditPassword($PasswordId)
    {
        $GeneratedArrayTitle = $this->encrypt($this->input->post('pass_title'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayUser = $this->encrypt($this->input->post('pass_user'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayPassword = $this->encrypt($this->input->post('pass_password'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayUrl = $this->encrypt($this->input->post('pass_url'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $GeneratedArrayNote = $this->encrypt(nl2br($this->input->post('pass_note')),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        
        
        $QueryResult = $this->db->query('UPDATE 
        {PREFIXDB}passwords
        SET
        pass_title = "'.$this->db->escape_str($GeneratedArrayTitle['password']).'",
        pass_user = "'.$this->db->escape_str($GeneratedArrayUser['password']).'",
        pass_password = "'.$this->db->escape_str($GeneratedArrayPassword['password']).'",
        pass_url = "'.$this->db->escape_str($GeneratedArrayUrl['password']).'",
        pass_note = "'.$this->db->escape_str($GeneratedArrayNote['password']).'",
        pass_date_updated = "'.$this->db->escape_str(date('Y-m-d H:i:s')).'"
        WHERE
        pass_id = "'.$this->db->escape_str($PasswordId).'"
        AND
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
        ');
    }
    
    public function SelectOneCategoryName($CategoryId)
    {
        // Kategoria
        
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_id = "'.$this->db->escape_str($CategoryId).'"
        AND
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        		
        foreach($QueryResult->result() as $row)
        {
            $QueryResultIs = $row->category_name;
            $QueryResultIdIs = $row->category_id;
            $QueryResultIdSubIs = $row->category_password_sub;
        }
        
        // LevelUp Category
        
        if($QueryResultIdSubIs != 0)
        {
            $QueryResult = $this->db->query('SELECT * FROM 
    		{PREFIXDB}category
            WHERE
            category_id = "'.$this->db->escape_str($QueryResultIdSubIs).'"
            AND
            category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    		');
            
            foreach($QueryResult->result() as $row)
            {
                $QueryResultIsHigh = $row->category_name;
                $QueryResultIdIsHigh = $row->category_id;
            }
        }
        
        if($QueryResultIdIsHigh != "")
        {
            $QueryResultTable[0]['name'] = $QueryResultIsHigh;
            $QueryResultTable[0]['id'] = $QueryResultIdIsHigh;
            
            $QueryResultTable[1]['name'] = $QueryResultIs;
            $QueryResultTable[1]['id'] = $QueryResultIdIs;
        }
        else
        {
            $QueryResultTable[0]['name'] = $QueryResultIs;
            $QueryResultTable[0]['id'] = $QueryResultIdIs;
        }
        
		return $QueryResultTable;
    }
    
    public function SelectOnePasswordView($PasswordId)
    {
        $QueryResultPass = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwords
        WHERE
        pass_id = "'.$this->db->escape_str($PasswordId).'"
        AND
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        return $QueryResultPass;
    }
    
    public function DeleteAllData()
    {
        // Usuwanie kategorii
        $QueryResult = $this->db->query('DELETE FROM 
  		{PREFIXDB}category
        WHERE
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    	');
        
        // Usuwanie haseł
        $QueryResult = $this->db->query('DELETE FROM 
    	{PREFIXDB}passwords
        WHERE
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    	');
        
        // Usuwanie pliku głównego
        $QueryResult = $this->db->query('DELETE FROM 
    	{PREFIXDB}passwordlist
        WHERE
        passwordlist_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    	');
    }
    
    public function SetUserNewPasswordInTheSystem()
    {
        //$this->decrypt($row->pass_title,$_SESSION['user_iv'],$_SESSION['user_password'])
        //$this->encrypt($this->input->post('pass_title'),base64_decode($_SESSION['user_iv']),$_SESSION['user_password']);
        $NotTimestampInFormat = date('Y-m-d H:i:s');
        
        // New Password
        
        $IvLength = openssl_cipher_iv_length('aes-256-cbc');
        $NewRepo_IV = openssl_random_pseudo_bytes($IvLength);
        
        $NewRepo_Password = $this->input->post('pass_password');

        //echo $NewRepo_Password;
        //
        // Main password
        //
        
            $QueryResult = $this->db->query('SELECT * FROM 
    		{PREFIXDB}passwordlist
            WHERE
            passwordlist_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
    		');
            
            foreach($QueryResult->result() as $row)
            {
                $MainPassword = $row->passwordlist_password;
                $MainIV = $row->passwordlist_iv;
            }
            
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}passwordlist
            SET
            passwordlist_password_old = "'.$this->db->escape_str($MainPassword).'",
            passwordlist_iv_old = "'.$this->db->escape_str($MainIV).'",
            passwordlist_change = "'.$this->db->escape_str($NotTimestampInFormat).'"
            WHERE
            passwordlist_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
            
        //
        // Main password - UPDATE
        //
            $GenerateNewPasswordArray = $this->encrypt($NewRepo_Password,$NewRepo_IV,$NewRepo_Password);
            //print_r($GenerateNewPasswordArray);
            
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}passwordlist
            SET
            passwordlist_password = "'.$this->db->escape_str($GenerateNewPasswordArray['password']).'",
            passwordlist_iv = "'.$this->db->escape_str($GenerateNewPasswordArray['iv']).'"
            WHERE
            passwordlist_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
        
        //
        // Category
        //
        
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        foreach($QueryResult->result() as $row)
        {
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}category
            SET
            category_name_old = "'.$this->db->escape_str($row->category_name).'",
            category_change = "'.$this->db->escape_str($NotTimestampInFormat).'"
            WHERE
            category_id = "'.$this->db->escape_str($row->category_id).'"
            AND
            category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
        }
        
        //
        // Category - UPDATE
        //
        
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}category
        WHERE
        category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        foreach($QueryResult->result() as $row)
        {
            $DecryptOldPassword = $this->decrypt($row->category_name,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewArray = $this->encrypt($DecryptOldPassword,$NewRepo_IV,$NewRepo_Password);
 
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}category
            SET
            category_name = "'.$this->db->escape_str($GenerateNewArray['password']).'"
            WHERE
            category_id = "'.$this->db->escape_str($row->category_id).'"
            AND
            category_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
        }
        
        
        //
        // Passwords
        //
        
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwords
        WHERE
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        foreach($QueryResult->result() as $row)
        {
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}passwords
            SET
            pass_title_old = "'.$this->db->escape_str($row->pass_title).'",
            pass_user_old = "'.$this->db->escape_str($row->pass_user).'",
            pass_password_old = "'.$this->db->escape_str($row->pass_password).'",
            pass_url_old = "'.$this->db->escape_str($row->pass_url).'",
            pass_note_old = "'.$this->db->escape_str($row->pass_note).'",
            pass_change = "'.$this->db->escape_str($NotTimestampInFormat).'"
            WHERE
            pass_id = "'.$this->db->escape_str($row->pass_id).'"
            AND
            pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
        }
        
        //
        // Password - UPDATE
        //
        $QueryResult = $this->db->query('SELECT * FROM 
		{PREFIXDB}passwords
        WHERE
        pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
		');
        
        foreach($QueryResult->result() as $row)
        {
            $DecryptOldTitle = $this->decrypt($row->pass_title,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewTitleArray = $this->encrypt($DecryptOldTitle,$NewRepo_IV,$NewRepo_Password);
            
            $DecryptOldUser = $this->decrypt($row->pass_user,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewUserArray = $this->encrypt($DecryptOldUser,$NewRepo_IV,$NewRepo_Password);
            
            $DecryptOldPassword = $this->decrypt($row->pass_password,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewPasswordArray = $this->encrypt($DecryptOldPassword,$NewRepo_IV,$NewRepo_Password);
            
            $DecryptOldUrl = $this->decrypt($row->pass_url,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewUrlArray = $this->encrypt($DecryptOldUrl,$NewRepo_IV,$NewRepo_Password);
            
            $DecryptOldNote = $this->decrypt($row->pass_note,$_SESSION['user_iv'],$_SESSION['user_password']);
            $GenerateNewNoteArray = $this->encrypt($DecryptOldNote,$NewRepo_IV,$NewRepo_Password);
            
            $QueryResult = $this->db->query('UPDATE 
            {PREFIXDB}passwords
            SET
            pass_title = "'.$this->db->escape_str($GenerateNewTitleArray['password']).'",
            pass_user = "'.$this->db->escape_str($GenerateNewUserArray['password']).'",
            pass_password = "'.$this->db->escape_str($GenerateNewPasswordArray['password']).'",
            pass_url = "'.$this->db->escape_str($GenerateNewUrlArray['password']).'",
            pass_note = "'.$this->db->escape_str($GenerateNewNoteArray['password']).'"
            WHERE
            pass_id = "'.$this->db->escape_str($row->pass_id).'"
            AND
            pass_password_id = "'.$this->db->escape_str($_SESSION['user_id']).'"
            ');
        }
    }
}

?>