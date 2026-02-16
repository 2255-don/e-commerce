-- Fix seller_profiles table: add status column
ALTER TABLE seller_profiles 
ADD COLUMN status ENUM('pending', 'approved', 'suspended', 'rejected') 
DEFAULT 'pending' 
AFTER shop_name;

-- Fix users table: update kyc_status ENUM
ALTER TABLE users 
MODIFY kyc_status ENUM('unverified', 'pending', 'verified', 'rejected') 
DEFAULT 'unverified';
