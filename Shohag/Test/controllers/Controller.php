<?php
// core/Controller.php

class Controller {
    
    // Load model
    protected function model($model) {
        require_once ROOT_PATH . '/models/' . $model . '.php';
        return new $model();
    }
    
    // Load view
    protected function view($view, $data = []) {
        extract($data);
        require_once ROOT_PATH . '/views/' . $view . '.php';
    }
    
    // Redirect
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit;
    }
    
    // Check if user is logged in
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Check if user is seeker
    protected function isSeeker() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'seeker';
    }
    
    // Require seeker login
    protected function requireSeeker() {
        if (!$this->isLoggedIn() || !$this->isSeeker()) {
            $_SESSION['error'] = 'Please login as a job seeker to continue';
            $this->redirect('login.php');
        }
    }
    
    // Set flash message
    protected function setFlash($type, $message) {
        $_SESSION['flash_' . $type] = $message;
    }
    
    // Get flash message
    protected function getFlash($type) {
        if (isset($_SESSION['flash_' . $type])) {
            $message = $_SESSION['flash_' . $type];
            unset($_SESSION['flash_' . $type]);
            return $message;
        }
        return null;
    }
    
    // Get POST data
    protected function post($key, $default = '') {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }
    
    // Get GET data
    protected function get($key, $default = '') {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
    
    // JSON response
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
