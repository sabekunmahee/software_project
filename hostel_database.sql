-- Hostel Management System Database - HMSDB
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hmsdb`
--
CREATE DATABASE IF NOT EXISTS `hmsdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hmsdb`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(100),
    mobile VARCHAR(15)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users(username, first_name, last_name, email, password, mobile)
VALUES
('abc@gmail.com','Sabekunnahar','Mahee','sabekunmahee@gmail.com','mahee1234','1234567891');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE rooms(
    room_number INT(20) PRIMARY KEY,
    capacity INT(20),
    occupied INT(20),
    status VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO rooms(room_number, capacity, occupied, status)
VALUES
(101,2,2,'Full'),
(102,2,1,'Partially Occupied'),
(103,2,1,'Partially Occupied'),
(104,2,0,'Vacant'),
(105,2,2,'Full'),
(106,2,2,'Full'),
(107,2,1,'Partially Occupied'),
(108,2,1,'Partially Occupied'),
(109,2,0,'Vacant'),
(110,2,1,'Partially Occupied');

-- --------------------------------------------------------

--
-- Table structure for table `new_student`
--

CREATE TABLE new_student(
    mobile_number VARCHAR(15) PRIMARY KEY,
    student_name VARCHAR(100),
    emergency_contact VARCHAR(15),
    room_number INT(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO new_student(mobile_number, student_name, emergency_contact, room_number)
VALUES
('01765457','Faria Jannat','04876568',101),
('012457669','Sujana Haque','014766877',102),
('016534578','Saima Tasnim','015457656',105);

-- --------------------------------------------------------

--
-- Table structure for table `up_students`
--

CREATE TABLE up_students(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    room_number INT(20),
    phone VARCHAR(15)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO up_students (name, email, room_number, phone)
VALUES
('Raida Haque','raida@gmail.com',103,'01234576'),
('Sanjida Tarin','sanjida@gmail.com',105,'01687645'),
('Moli Roy','moli@gmail.com',108,'013456766');

-- --------------------------------------------------------

--
-- Table structure for table `visitors_log`
--

CREATE TABLE visitors_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    visitor_name VARCHAR(100) NOT NULL,
    visitor_relation VARCHAR(50) NOT NULL,
    visitor_mobile VARCHAR(15) NOT NULL,
    visit_purpose VARCHAR(200) NOT NULL,
    time_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    time_out TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO visitors_log (student_name, room_number, visitor_name, visitor_relation, visitor_mobile, visit_purpose, time_in, time_out) VALUES
('Rakiba Akter', '101', 'Karim Ahmed', 'Father', '01711111111', 'Giving money and clothes', '2024-01-15 10:30:00', '2024-01-15 11:45:00'),
('Sadia Akter', '205', 'Fatima Begum', 'Mother', '01822222222', 'Medical checkup', '2024-01-15 14:00:00', '2024-01-15 15:30:00'),
('Sahana Hossain', '112', 'Jamil Hossain', 'Brother', '01933333333', 'Weekend visit', '2024-01-14 16:20:00', '2024-01-14 18:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100),
    room_number INT,
    mobile_number VARCHAR(20),
    complaint_text TEXT,
    admin_comment TEXT,
    responded_by VARCHAR(50),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    response_time DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO complaints 
(student_name, room_number, mobile_number, complaint_text, admin_comment, responded_by, status, created_at, response_time)
VALUES
('Rakiba Akter', 101, '01711112222', 'The fan in my room is not working properly.', 'Electrician has been informed, will fix by tomorrow.', 'Admin', 'In Progress', '2025-11-09 10:15:00', '2025-11-09 12:45:00'),
('Sadia Akter', 202, '01733334444', 'There is no water supply since morning.', 'Water tank issue resolved, please confirm.', 'Admin', 'Resolved', '2025-11-08 09:00:00', '2025-11-08 11:20:00'),
('Jesmin Jara', 105, '01855556666', 'WiFi connection is very weak in my room.', NULL, NULL, 'Pending', '2025-11-10 08:30:00', NULL),
('Mim Akter', 109, '01777778888', 'Light in corridor keeps blinking.', 'Electric maintenance scheduled at 6 PM today.', 'Admin', 'In Progress', '2025-11-09 14:00:00', '2025-11-09 15:45:00'),
('Samjida Islam', 203, '01999990000', 'Food served last night was cold and late.', NULL, NULL, 'Pending', '2025-11-10 09:10:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studentfees`
--

CREATE TABLE studentfees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    room_number INT(10) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    month ENUM('July', 'August', 'September', 'October', 'November') NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    due_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) DEFAULT 6000.00,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO studentfees (student_name, room_number, mobile_number, month, amount_paid, due_amount) VALUES
('Rakiba Akter', '101', '01712345678', 'July', 6000, 0),
('Rakiba Akter', '101', '01712345678', 'August', 5000, 1000),
('Rakiba Akter', '101', '01712345678', 'September', 6000, 0),
('Rakiba Akter', '101', '01712345678', 'October', 4000, 2000),
('Rakiba Akter', '101', '01712345678', 'November', 0, 6000),
('Sadia Akter', '205', '01822222222', 'July', 6000, 0),
('Sadia Akter', '205', '01822222222', 'August', 6000, 0),
('Sadia Akter', '205', '01822222222', 'September', 3000, 3000),
('Sadia Akter', '205', '01822222222', 'October', 6000, 0),
('Sadia Akter', '205', '01822222222', 'November', 2000, 4000),
('Kamola Hossain', '112', '01933333333', 'July', 0, 6000),
('Kamola Hossain', '112', '01933333333', 'August', 0, 6000),
('Kamola Hossain', '112', '01933333333', 'September', 6000, 0),
('Kamola Hossain', '112', '01933333333', 'October', 6000, 0),
('Kamola Hossain', '112', '01933333333', 'November', 6000, 0),
('Jaida Islam', '103', '01755667788', 'July', 6000, 0),
('Jaida Islam', '103', '01755667788', 'August', 6000, 0),
('Jaida Islam', '103', '01755667788', 'September', 6000, 0),
('Jaida Islam', '103', '01755667788', 'October', 6000, 0),
('Jaida Islam', '103', '01755667788', 'November', 6000, 0),
('Tania Akter', '105', '01988776655', 'July', 6000, 0),
('Tania Akter', '105', '01988776655', 'August', 6000, 0),
('Tania Akter', '105', '01988776655', 'September', 6000, 0),
('Tania Akter', '105', '01988776655', 'October', 6000, 0),
('Tania Akter', '105', '01988776655', 'November', 6000, 0);

-- --------------------------------------------------------

--
-- Table structure for table `leaved_students`
--

CREATE TABLE leaved_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    room_number INT(20) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    leave_reason VARCHAR(200) NOT NULL,
    leave_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO leaved_students (student_name, room_number, mobile_number, leave_reason, leave_date) VALUES
('Rahima Khatun', '101', '01712345678', 'Completed studies', '2024-01-10'),
('Sadia Islam', '205', '01822222222', 'Completed studies', '2024-01-12'),
('Kamola Hossain', '112', '01933333333', 'Family reasons', '2024-01-08'),
('Fatima Begum', '308', '01755667788', 'Medical leave', '2024-01-05');

-- --------------------------------------------------------

--
-- Table structure for table `student_login`
--

CREATE TABLE student_login (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(100) NOT NULL,
  password VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO student_login (student_name, password) VALUES
('Rakiba Akter', '12345'),
('Sadia Akter', 'pass2025'),
('Jaida Islam', 'abcd1234');

-- --------------------------------------------------------

--
-- Table structure for table `studentprofile`
--

CREATE TABLE studentprofile (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(100) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  mobile_number VARCHAR(20),
  emergency_contact VARCHAR(20),
  room_number INT(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO studentprofile (student_name, `password`, mobile_number, emergency_contact, room_number)
VALUES
('Rakiba Akter', '12345', '01711112222', '01888889999', '101'),
('Sadia Akter', 'pass2025', '01755557777', '01899998888', '102'),
('Jaida Islam', 'abcd1234', '01922224444', '01777775555', '103');

-- --------------------------------------------------------

--
-- Table structure for table `fee_payments`
--

CREATE TABLE fee_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    payment_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(50) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO fee_payments (student_name, payment_amount, payment_method, transaction_id, payment_date) VALUES
('Rakiba Akter', 1000, 'Bikash', 'BKS784512', '2024-01-05 10:30:00'),
('Rakiba Akter', 1000, 'Nagad', 'NGD451236', '2024-01-12 14:20:00'),
('Sadia Akter', 1500, 'Bikash', 'BKS963258', '2024-01-08 11:15:00'),
('Jaida Islam', 3000, 'Bank Transfer', 'BNK784512', '2024-01-03 09:45:00'),
('Jaida Islam', 3000, 'Cash', 'CSH123456', '2024-01-10 16:30:00'),
('Arifa Khatun', 2000, 'Bikash', 'BKS852147', '2024-01-06 13:25:00'),
('Arifa Khatun', 2000, 'Nagad', 'NGD753159', '2024-01-15 10:10:00'),
('Tania Akter', 6000, 'Bank Transfer', 'BNK951753', '2024-01-02 08:20:00'),
('Soheli Rana', 2000, 'Bikash', 'BKS456123', '2024-01-09 15:40:00'),
('Nusrat Jahan', 3000, 'Nagad', 'NGD258369', '2024-01-04 12:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `complaintstudent`
--

CREATE TABLE complaintstudent (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    complaint_text TEXT NOT NULL,
    admin_reply TEXT NULL,
    status ENUM('Pending', 'Resolved') DEFAULT 'Pending',
    date_submitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_replied TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO complaintstudent (student_name, complaint_text, admin_reply, status, date_submitted, date_replied) VALUES
('Rakiba Akter', 'The fan in my room is not working properly.', 'We have scheduled maintenance for tomorrow. Our electrician will fix it.', 'Resolved', '2024-01-15 10:30:00', '2024-01-15 14:20:00'),
('Sadia Akter', 'WiFi connection is very slow in room 101.', NULL, 'Pending', '2024-01-16 09:15:00', NULL),
('Jaida Islam', 'Water leakage in bathroom from ceiling.', 'Plumber has been assigned. Will be fixed today.', 'Resolved', '2024-01-14 16:45:00', '2024-01-14 18:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO accounts (username, password, full_name, email) VALUES
('accounts', '123456', 'Accounts Manager', 'accounts@hostel.com');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100),
    class VARCHAR(50),
    month VARCHAR(20) NOT NULL,
    monthly_fee DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    due_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_status ENUM('Paid', 'Partial', 'Due') DEFAULT 'Due',
    admin_email VARCHAR(100) DEFAULT 'mahee@gmail.com',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO reports (student_name, student_id, phone, email, class, month, monthly_fee, amount_paid, due_amount, payment_status) VALUES
('Rakiba Akter', 'STU101', '01712345678', 'rakib@student.edu.bd', 'Class 10', 'August', 6000, 6000, 0, 'Paid'),
('Rakiba Akter', 'STU101', '01712345678', 'rakib@student.edu.bd', 'Class 10', 'September', 6000, 4000, 2000, 'Partial'),
('Rakiba Akter', 'STU101', '01712345678', 'rakib@student.edu.bd', 'Class 10', 'October', 6000, 6000, 0, 'Paid'),
('Rakiba Akter', 'STU101', '01712345678', 'rakib@student.edu.bd', 'Class 10', 'November', 6000, 0, 6000, 'Due'),
('Sadia Akter', 'STU205', '01822222222', 'sadia@student.edu.bd', 'Class 9', 'August', 6000, 6000, 0, 'Paid'),
('Sadia Akter', 'STU205', '01822222222', 'sadia@student.edu.bd', 'Class 9', 'September', 6000, 3000, 3000, 'Partial'),
('Sadia Akter', 'STU205', '01822222222', 'sadia@student.edu.bd', 'Class 9', 'October', 6000, 6000, 0, 'Paid'),
('Sadia Akter', 'STU205', '01822222222', 'sadia@student.edu.bd', 'Class 9', 'November', 6000, 3000, 3000, 'Partial'),
('Sahana Hossain', 'STU112', '01933333333', 'sahana@student.edu.bd', 'Class 11', 'August', 6000, 0, 6000, 'Due'),
('Sahana Hossain', 'STU112', '01933333333', 'sahana@student.edu.bd', 'Class 11', 'September', 6000, 0, 6000, 'Due'),
('Sahana Hossain', 'STU112', '01933333333', 'sahana@student.edu.bd', 'Class 11', 'October', 6000, 0, 6000, 'Due'),
('Sahana Hossain', 'STU112', '01933333333', 'sahana@student.edu.bd', 'Class 11', 'November', 6000, 0, 6000, 'Due'),
('Jaida Khatun', 'STU103', '01755667788', 'jaida@student.edu.bd', 'Class 10', 'August', 6000, 6000, 0, 'Paid'),
('Jaida Khatun', 'STU103', '01755667788', 'jaida@student.edu.bd', 'Class 10', 'September', 6000, 6000, 0, 'Paid'),
('Jaida Khatun', 'STU103', '01755667788', 'jaida@student.edu.bd', 'Class 10', 'October', 6000, 6000, 0, 'Paid'),
('Jaida Khatun', 'STU103', '01755667788', 'jaida@student.edu.bd', 'Class 10', 'November', 6000, 6000, 0, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `feescollection`
--

CREATE TABLE feescollection (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    room_number INT(10) NOT NULL,
    mobile_number VARCHAR(15) NOT NULL,
    monthly_fee DECIMAL(10,2) DEFAULT 6000.00,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    due_amount DECIMAL(10,2) DEFAULT 6000.00,
    last_payment_date DATE NULL,
    status ENUM('Paid', 'Partial', 'Due') DEFAULT 'Due',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO feescollection (student_name, room_number, mobile_number, paid_amount, due_amount, status) VALUES
('Rakiba Akter', '101', '01712345678', 6000, 2000, 'Partial'),
('Sadia Akter', '205', '01822222222', 6000, 1500, 'Partial'),
('Jesmin Hossain', '112', '01933333333', 0, 6000, 'Due'),
('Jahida Islam', '103', '01755667788', 6000, 0, 'Paid'),
('Arifa Khatun', '201', '01899887766', 4000, 2000, 'Partial'),
('Tania Akter', '105', '01988776655', 6000, 0, 'Paid'),
('Soheli Rana', '401', '01744556677', 2000, 4000, 'Partial'),
('Nusrat Jahan', '302', '01877665544', 6000, 0, 'Paid'),
('Ima Hasan', '208', '01955443322', 0, 6000, 'Due'),
('Mitu Rahman', '115', '01733445566', 6000, 0, 'Paid'),
('Rajia Hossain', '106', '01811223344', 5000, 1000, 'Partial'),
('Sumaiya Akter', '209', '01966554433', 0, 6000, 'Due'),
('Fahima Akhter', '303', '01777889900', 6000, 0, 'Paid'),
('Jannatul Ferdous', '107', '01844556677', 3000, 3000, 'Partial'),
('Nadia Sultana', '304', '01788990011', 0, 6000, 'Due'),
('Rafia Hossain', '108', '01855667788', 4000, 2000, 'Partial'),
('Tanjila Islam', '211', '01933445566', 6000, 0, 'Paid'),
('Mamuni Rashid', '305', '01799001122', 6000, 0, 'Paid'),
('Sabrina Chowdhury', '109', '01866778899', 0, 6000, 'Due');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;