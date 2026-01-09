# INSTRUCTIONS FOR AI CHATBOT #
Please read the following and execute exactly what is stated.

## Project Overview ##
A comprehensive student leadership evaluation and portfolio management system built with Laravel 12 and Filament 4, designed for St. Paul University Philippines (SPUP) student government organizations.

## Core MVP ##
### Multi-Panel Architecture ###
Admin Panel (/admin) - Administrative management interface
Student Panel (/student) - Student portfolio and evaluation interface
Dual authentication system (Users for admin, Students for portfolio)

### User Management System ###
Technical Stack:
Authentication: Laravel Sanctum + Filament Auth
Authorization: Spatie Laravel Permission package
Role-based Access Control: Admin, Student roles

Entities:
Users (Administrators/Faculty)
Organization assignment
School number system
Email-based authentication
Students (Evaluated participants)
E-portfolio capabilities (profile pictures, bio)
Independent authentication system
School number integration

### Organization Management ###
Multi-organizational support for different SPUP departments:
1. Paulinian Student Government (main)
2. PSG - SITE (School of IT & Engineering)
3. PSG - SBAHM (School of Business)
4. PSG - SNAHS (School of Nursing & Allied Health)
5. PSG - SASTE (School of Arts, Sciences & Teacher Education)
Logo and branding per organization
Hierarchical user assignment

## Comprehensive Evaluation System ##
Technical Implementation:
360-degree evaluation model: Self, Peer, and Adviser evaluations
Weighted scoring algorithm:
Adviser: 65% (includes length of service)
Peer: 25%
Self: 10%
Dynamic form generation using Filament schemas
Position-based evaluations (President, VP, Secretary, etc.)

### Advanced Ranking System ###
Algorithmic Ranking:
Tier-based classification:
Gold: ≥2.41 points
Silver: 1.81-2.40 points
Bronze: 1.21-1.80 points
None: <1.21 points
Automatic rank computation via Eloquent observers
Real-time dashboard widgets showing rank distributions


## TECHNICAL ARCHITECTURE ##

### Backend Stack ###
* Framework: Laravel 12 (PHP 8.2+)
* Admin Interface: Filament 4.x
* Database: MySQL (with comprehensive migrations)
* Authentication: Multi-guard system
* Testing: Pest PHP framework

### Database Schema ###
Key Tables
* users - Admin/faculty accounts with organization links
* students - Student accounts with e-portfolio fields
* evaluations - Year-based evaluation cycles
* evaluation_scores - Individual question responses with JSON storage
* ranks - Computed final rankings with breakdown
* certificates - Generated certificates tracking

# TASKS #
1. When clicking on a student in the student table of the admin panel student resource, it should show the student's portfolio (Similar layout to the profile infolist in the student panel profile resource).
2. I basically want it to be like that. Example scenario: I want to view a student. I go to students, click on a studnet, I see their portfolio, and there is an edit button if I want to change their details.

# NOTES #
1. Make sure to use only filament. No using custom blade files.
