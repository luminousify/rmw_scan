<?php

/**
 * ResponseHelper
 * Utility class for consistent API responses
 */
class ResponseHelper
{
    /**
     * Send JSON success response
     */
    public static function success($message, $data = null, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response);
        exit();
    }
    
    /**
     * Send JSON error response
     */
    public static function error($message, $httpCode = 400, $errors = null)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'error' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response);
        exit();
    }
    
    /**
     * Validate required fields
     */
    public static function validateRequired($data, $requiredFields)
    {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }
    
    /**
     * Set JSON response headers
     */
    public static function setJsonHeaders()
    {
        header('Content-Type: application/json');
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}
