<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PrivilegesStoredFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('drop function if exists UserHasPrivilegeTo;');
        DB::unprepared('CREATE FUNCTION UserHasPrivilegeTo(USER INT UNSIGNED, privilege_slug VARCHAR(255)) RETURNS INT UNSIGNED DETERMINISTIC READS SQL DATA BEGIN DECLARE result INT(1) DEFAULT 0; DECLARE role BIGINT; DECLARE privilege BIGINT; DECLARE bDone INT(1) DEFAULT 0;
    DECLARE countUR INT; DECLARE countR INT; DECLARE countU INT;
    DECLARE roles CURSOR FOR SELECT role_id FROM user_role WHERE user_id = USER;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET bDone = 1;
    SELECT id INTO privilege FROM privileges WHERE ((slug LIKE privilege_slug) COLLATE utf8mb4_unicode_ci);
    SET result = 0; SET countU = 0; SET countUR = 0;
    SELECT COUNT(*) INTO countUR FROM user_restricted_privilege WHERE user_id = USER AND privilege_id = privilege;
    IF countUR < 1 THEN
        SELECT COUNT(*) INTO countU FROM user_privilege WHERE user_id = USER AND privilege_id = privilege;
        IF countU >= 1 THEN
            SET result = 1;
        ELSE
            OPEN roles; SET bDone = 0;
            REPEAT
                FETCH roles INTO role; SET countR = 0;
                SELECT COUNT(*) INTO countR FROM role_privilege WHERE role_id = role AND privilege_id = privilege;
                IF countR >= 1 THEN SET bDone = 1; END IF;
            UNTIL bDone END REPEAT;
            IF countR >= 1 THEN SET result = 1; END IF;
        END IF;
    END IF;
    RETURN (result); END;');
        DB::unprepared('drop PROCEDURE if exists UsersWithPrivilege;');
        DB::unprepared('CREATE PROCEDURE UsersWithPrivilege(privilege_slug VARCHAR(255)) BEGIN DECLARE privilege BIGINT; DROP TEMPORARY TABLE IF EXISTS UsersWithPrivilege; SELECT id INTO privilege FROM privileges WHERE ((slug LIKE privilege_slug)); CREATE TEMPORARY TABLE UsersWithPrivilege AS SELECT user_id FROM user_role WHERE role_id IN (SELECT role_id FROM role_privilege WHERE privilege_id = privilege) UNION SELECT user_id from user_privilege where privilege_id = privilege; DELETE FROM UsersWithPrivilege WHERE user_id IN (SELECT user_id from user_restricted_privilege WHERE privilege_id = privilege); CREATE TEMPORARY TABLE Users AS SELECT * FROM users WHERE id IN (SELECT user_id FROM UsersWithPrivilege); SELECT DISTINCT * FROM Users; END;');
        DB::unprepared('drop PROCEDURE if exists UsersWithoutPrivilege;');
        DB::unprepared('create procedure UsersWithoutPrivilege(IN privilege_slug varchar(255)) BEGIN DECLARE privilege BIGINT; DROP TEMPORARY TABLE IF EXISTS UsersWithPrivilege; DROP TEMPORARY TABLE IF EXISTS Users; SELECT id INTO privilege FROM privileges WHERE ((slug LIKE privilege_slug) COLLATE utf8mb4_unicode_ci); CREATE TEMPORARY TABLE UsersWithPrivilege AS SELECT user_id FROM user_role WHERE role_id IN (SELECT role_id FROM role_privilege WHERE privilege_id = privilege) UNION SELECT user_id from user_privilege where privilege_id = privilege; DELETE FROM UsersWithPrivilege WHERE user_id IN (SELECT user_id from user_restricted_privilege WHERE privilege_id = privilege); CREATE TEMPORARY TABLE Users AS SELECT * FROM users WHERE id NOT IN (SELECT user_id FROM UsersWithPrivilege); SELECT DISTINCT * FROM Users; END;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('drop function if exists UserHasPrivilegeTo;');
        DB::unprepared('drop PROCEDURE if exists UsersWithPrivilege;');
        DB::unprepared('drop PROCEDURE if exists UsersWithoutPrivilege;');
    }
}
