<?php

namespace App\class;
use function doctrine;

class auth
{
    private $conn;

    public function __construct()
    {
    }

    public function register($firstName, $lastName, $email, $password)
    {
        try {
              // Check if email already exists
              $qb = doctrine();
              $qb->select('u.id')
                  ->from('users', 'u')
                  ->where('u.email = :email')
                  ->setParameter('email', $email);
  
              $existingUser = $qb->executeQuery()->fetchAssociative();
  
              if ($existingUser) {
                  return [
                      'success' => false,
                      'message' => 'Email already registered!'
                  ];
              }
  
              // Hash password
              $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  
              // Insert new user
              $qb = doctrine();
              $qb->insert('users')
                  ->values([
                      'firstName' => ':firstName',
                      'lastName'  => ':lastName',
                      'email'     => ':email',
                      'password'  => ':password',
                  ])
                  ->setParameter('firstName', $firstName)
                  ->setParameter('lastName', $lastName)
                  ->setParameter('email', $email)
                  ->setParameter('password', $hashedPassword);
  
              $qb->executeStatement();
  
              return [
                  'success' => true,
                  'message' => 'Registration successful! Please login.'
              ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ];
        }
    }

    public function login($email, $password, $remember = false)
    {
        try {
            // Get user by email
            $qb = doctrine();
            $qb->select('id, firstName, lastName, email, password')
                ->from('users', 'u')
                ->where('u.email = :email')
                ->setParameter('email', $email);

            $user = $qb->executeQuery()->fetchAssociative();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password!'
                ];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password!'
                ];
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];

            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
                
                // Store token in database (you might want to add a remember_tokens table)
                // For now, we'll just set the session
            }

            return [
                'success' => true,
                'message' => 'Login successful!'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ];
        }
    }

    public function logout()
    {
        // Clear session
        session_destroy();
        
        // Clear remember me cookie
        setcookie('remember_token', '', time() - 3600, '/');
        
        return [
            'success' => true,
            'message' => 'Logged out successfully!'
        ];
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            $qb = doctrine();
            $qb->select('*')
                ->from('users', 'u')
                ->where('u.id = :id')
                ->setParameter('id', $_SESSION['user_id']);

            $user = $qb->executeQuery()->fetchAssociative();
            return $user;
        } catch (\Exception $e) {
            return null;
        }
    }
}
