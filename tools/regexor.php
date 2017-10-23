<?php
    $src = file_get_contents('regexor.sql');
  
    // we need to replace all escaped quotes or the regex below will go nuts
    $src = str_replace(['\\"', "\\'"], ['[Q1]', '[Q1]'], $src);
    
    $regexes = [
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Init DB[ \t]+travian\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SET NAMES \'UTF8\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT Count\(\*\) as Total FROM s1_users WHERE timestamp > \d{0,100} AND tribe!=\d{1,2} AND tribe!=\d{1,2} AND tribe!=\d{1,2}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users WHERE access<( )?8 AND id > \d{1,1000} AND tribe<=3 AND tribe > 0 ORDER BY (oldrank|ap|dp|clp|RR) (ASC|DESC)(, id DESC)? Limit 1(0)?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Quit[ \t]+\n?/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT username FROM s1_users where username = \'[^\']+\' LIMIT 1\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id,password,sessid,is_bcrypt FROM s1_users where username = \'[^\']+\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (act|vac_mode|quest) FROM s1_users where username = \'[^\']+\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users where (username = \'[^\']+\'|tribe = \d{0,100})\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users SET vac_mode = \'\d{0,100}\' , vac_time=\'\d{0,100}\' WHERE id=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT IGNORE INTO s1_online \(name, uid, time, sit\) VALUES \(\'[^\']+\', \d{0,100}, \'\d{0,100}\', \d{0,100}\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id, village_select FROM `s1_users` WHERE `username`=\'[^\']+\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM `s1_vdata` WHERE `wref` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT wref from s1_vdata where owner = \d{0,100} order by capital DESC,pop DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT sit(2)? FROM s1_online where uid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT SUM\(hero\) from s1_enforcement where `from` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (SUM\(hero\)|\*) from s1_units where (`)?vref(`)?( )?=( )?(\')?\d{0,100}(\')?\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT SUM\(t\d{0,100}\) from s1_prisoners where `from` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.from = \'\d{0,100}\' and s1_movement.ref = s1_attacks.id and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.to = \'\d{0,100}\' and s1_movement.ref = s1_attacks.id and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT dead FROM s1_hero WHERE `uid` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT inrevive FROM s1_hero WHERE `uid` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT intraining FROM s1_hero WHERE `uid` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+REPLACE into s1_active values \(\'[^\']+\',\d{0,100}\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users set sessid = \'[^\']+\' where username = \'[^\']+\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+Insert into s1_login_log values \(\d{0,100},\d{0,100},\'[^\']+\'\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Connect[ \t]+[^@]+@[a-zA-Z]+ as anonymous on \n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users set timestamp = (\')?\d{0,100}(\')? where username = \'[^\']+\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata WHERE target IN\([^)]+\) and send = 0 and archived = 0 ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata WHERE owner IN\([^)]+\) ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata WHERE target IN\([^)]+\) and send = 0 and archived = 0 and deltarget = 0 ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata WHERE owner IN\([^)]+\) and delowner = 0 ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata where target IN\([^)]+\) and send = 0 and archived = 1\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_mdata where target IN\([^)]+\) and send = 0 and archived = 1 and deltarget = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_ndata where uid = \d{0,100} ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_ndata where uid = \d{0,100} and del = 0 ORDER BY time DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (\*|evasion|name) FROM s1_vdata where wref = (\'?)\d{0,100}(\')?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (\*|f\d{0,100}t) from s1_fdata where vref = \d{0,100}( LIMIT 1)?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT x,y FROM s1_wdata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id, fieldtype FROM s1_wdata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_odata where conqured = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_units where vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_enforcement where (\`)?vref(\`)?( )?=( )?\d{0,100}\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_enforcement where `from` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT e.\*,o.conqured FROM s1_enforcement as e LEFT JOIN s1_odata as o ON e.vref=o.wref where o.conqured = \d{0,100} AND e.from !=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT e.\*,o.conqured FROM s1_enforcement as e LEFT JOIN s1_odata as o ON e.vref=o.wref where o.conqured = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_prisoners where (s1_prisoners.)?(`)?from(`)? = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (tribe|plus|gold|alliance|b4|id|maxevasion|username|\*) FROM s1_users where (id = (\')?\d{0,100}(\')?|username = \'[^\']+\'|tribe = \d{0,100})( ORDER BY (ap|dp|clp|RR) DESC(, id DESC)? Limit 1)?\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement where s1_movement.from = \'\d{0,100}\' and sort_type = 5 and proc = 0 ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_tdata where vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_abdata where vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_research where vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata where wid = \d{0,100}( and field = \d{0,100})? and master = \d{0,100}( order by master,timestamp ASC)?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts WHERE vref = \'\d{0,100}\' AND type = \'\d{0,100}\' order by size\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts WHERE owner = \d{0,100} AND type = \d{0,100} AND size=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts WHERE owner = \d{0,100} AND active = 1 AND type = \d{0,100} AND size=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts WHERE vref = \d{0,100} AND active = 1 AND type = \d{0,100} AND size=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts WHERE vref = \d{0,100} AND \(\(type = \d{0,100} AND kind = \d{0,100}\) OR \(owner = \d{0,100} AND size > 1 AND active = 1 AND type = \d{0,100} AND kind = \d{0,100}\)\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT wood,clay,iron,crop,maxstore,maxcrop from s1_(v|o)data where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set wood = (-)?\d{0,100}(\.\d{0,100})?, clay = (-)?\d{0,100}(\.\d{0,100})?, iron = (-)?\d{0,100}(\.\d{0,100})?, crop = (-)?\d{0,100}(\.\d{0,100})? where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set lastupdate = \d{0,100} where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata where wid = \d{0,100} order by master,timestamp ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT count\(id\) FROM s1_users where id > 5\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT s1_users.id userid,( s1_users.tribe tribe,)? s1_users.username username(, s1_users.oldrank oldrank)?,( )?s1_users.alliance alliance, \(\n[^A-Za-z]+SELECT SUM\( s1_vdata.pop \)\n[^A-Za-z]+FROM s1_vdata\n[^A-Za-z]+WHERE s1_vdata.owner = userid\n[^A-Za-z]+\)totalpop, \(\n[^A-Za-z]+SELECT COUNT\( s1_vdata.wref \)\n[^A-Za-z]+FROM s1_vdata\n[^A-Za-z]+WHERE s1_vdata.owner = userid AND type != 99\n[^A-Za-z]+\)totalvillages, \(\n[^A-Za-z]+SELECT s1_alidata.tag\n[^A-Za-z]+FROM s1_alidata, s1_users\n[^A-Za-z]+WHERE s1_alidata.id = s1_users.alliance\n[^A-Za-z]+AND s1_users.id = userid\n[^A-Za-z]+\)allitag\n[^A-Za-z]+FROM s1_users\n[^A-Za-z]+WHERE( s1_users.access < 8\n[^A-Za-z]+ AND)? s1_users.tribe (<=|=) \d{0,100}( AND s1_users.access < \d{0,100})?\n[^A-Za-z]+AND s1_users.id > 5\n[^A-Za-z]+ORDER BY totalpop DESC, totalvillages DESC, userid DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_medal order by week DESC LIMIT 0, 1\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users where oldrank = 0 and id > 5\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+DELETE FROM s1_active WHERE timestamp < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_odata WHERE wood < \d{0,100} OR clay < \d{0,100} OR iron < \d{0,100} OR crop < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_odata set wood = (-)?\d{0,100}(\.\d{0,100})?, clay = (-)?\d{0,100}(\.\d{0,100})?, iron = (-)?\d{0,100}(\.\d{0,100})?, crop = (-)?\d{0,100}(\.\d{0,100})? where wref = \d{0,100}(\.\d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_odata set lastupdated = \d{0,100} where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_vdata WHERE maxstore < \d{0,100} OR maxcrop < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_vdata WHERE wood > maxstore OR clay > maxstore OR iron > maxstore OR crop > maxcrop\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_vdata WHERE wood < 0 OR clay < 0 OR iron < 0 OR crop < 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_odata WHERE maxstore < \d{0,100} OR maxcrop < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM `s1_ww_attacks` WHERE `attack_time` <= \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id, lastupdate FROM s1_users WHERE lastupdate < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT sum\(cp\) FROM s1_vdata where owner = \d{0,100} and natar = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users set cp = cp \+ \d{0,100}(\.\d{0,100})?, lastupdate = \d{0,100} where id = \'\d{0,100}\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_hero( WHERE uid = \d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_hero WHERE (`)?dead(`)?( )?=( )?(\')?\d{0,100}(\')?( AND (uid|`heroid`)=\d{0,100}( LIMIT 1)?)?\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE `s1_hero` SET health = \'\d{0,100}\' WHERE heroid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE `s1_hero` SET lastupdate = \'\d{0,100}\' WHERE heroid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE `s1_hero` SET health = \'\d{0,100}(\.\d{0,100})?\' WHERE heroid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (uid|timestamp) FROM s1_deleting where (timestamp < \d{0,100}|uid = \d{0,100})\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata where timestamp < \d{0,100} and master = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT f\d{0,100} from s1_fdata where vref = \d{0,100} LIMIT 1\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_fdata set (f\d{0,100} = \d{0,100}, )?f\d{0,100}t( )?=( )?\d{0,100} where vref( )?=( )?\d{0,100}\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set cp = \d{0,100} where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set pop = \d{0,100} where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT owner FROM s1_vdata where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users WHERE access < 8\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users set (clp|dpall|apall|dp|ap|RR) = (clp|dpall|apall|dp|ap|RR) [+-] (-)?\d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users set oldrank = \d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id,name,tag,oldrank,Aap,Adp FROM s1_alidata where id != \'\' ORDER BY id DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users where alliance = \d{0,100} order  by \(SELECT sum\(pop\) FROM s1_vdata WHERE owner =  s1_users.id\) desc, s1_users.id desc\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT sum\(pop\) FROM s1_vdata where owner = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_bdata set loopcon = 0 where loopcon = 1 and master = 0 and wid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+DELETE FROM s1_bdata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (type|count\(\*\)) FROM `s1_odata` WHERE conqured( )?=( )?\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT starv FROM s1_vdata where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set starv = \'\d{0,100}\' where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set starvupdate = \'\d{0,100}\' where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_alidata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_alidata set (clp|Adp|Aap|dp|ap|RR) = (clp|Adp|Aap|dp|ap|RR) [+-] (-)?\d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_alidata set oldrank = \d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_bdata set loopcon = 0 where loopcon = 1 and master = 0 and wid = \d{0,100} and field [<>] \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata WHERE master = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (wood|clay|iron|crop) FROM s1_vdata where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata where wid = \d{0,100} and type = \d{0,100} and master = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_bdata where wid = \d{0,100} and field [<>] \d{0,100} and master = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_demolition WHERE (timetofinish<=\d{0,100}|vref=\d{0,100})\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM `s1_fdata`\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE `s1_vdata` SET `maxstore` = \d{0,100}, `maxcrop` = \d{0,100} WHERE `wref` = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+DELETE from s1_route where timeleft < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_route where timestamp < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_send where s1_movement.ref = s1_send.id and s1_movement.proc = 0 and sort_type = 0 and endtime < \d{0,100}(\.\d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement where proc = 0 and sort_type = 2 and endtime < \d{0,100}(\.\d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_research where timestamp < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_training where vref IS NOT NULL\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_units set ((u\d{0,100}(o)?|hero) = (u\d{0,100}(o)?|hero) [+-]  (-)?\d{0,100}(,)?( )?)* WHERE vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_training set amt = amt - \d{0,100}, timestamp2 = timestamp2 \+ \d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_vdata where starv != 0 and owner != \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT e.\*,o.conqured,o.wref,o.high, o.owner as ownero, v.owner as ownerv FROM s1_enforcement as e LEFT JOIN s1_odata as o ON e.vref=o.wref LEFT JOIN s1_vdata as v ON e.from=v.wref where o.conqured=\d{0,100} AND o.owner(<>|=)v.owner\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT e.\*, v.owner as ownerv, v1.owner as owner1 FROM s1_enforcement as e LEFT JOIN s1_vdata as v ON e.from=v.wref LEFT JOIN s1_vdata as v1 ON e.vref=v1.wref where e.vref=\d{0,100} AND v.owner(<>|=)v1.owner\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_vdata set crop = \'\d{0,100}\' where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_vdata where celebration < \d{0,100} AND celebration != 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.ref = s1_attacks.id and s1_movement.proc = \'0\' and s1_movement.sort_type = \'\d{0,100}\' and s1_attacks.attack_type != \'\d{0,100}\' and endtime < \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id, oasistype FROM s1_wdata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_wdata left JOIN s1_vdata ON s1_vdata.wref = s1_wdata.id where s1_wdata.id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.to = \'\d{0,100}\' and s1_movement.ref = s1_attacks.id and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} or s1_movement.to = \'\d{0,100}\' and s1_movement.ref = s1_attacks.id and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_units set hero = hero -   WHERE vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_attacks set (t1=t1 - \d{0,100},( )?t2=t2 - \d{0,100},( )?t3=t3 - \d{0,100},( )?t4=t4 - \d{0,100},( )?t5=t5 - \d{0,100},( )?t6=t6 - \d{0,100},( )?t7=t7 - \d{0,100},( )?t8=t8 - \d{0,100},( )?t9=t9 - \d{0,100},( )?t10=t10 - \d{0,100},( )?t11=t11 - \d{0,100})*? WHERE id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT capital,wref,name,pop,created from s1_vdata where owner = \d{0,100} order by pop desc\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT INTO s1_ndata \(id, uid, toWref, ally, topic, ntype, data, time, viewed\) values \(0,\'\d{0,100}\',\'\d{0,100}\',\'\d{0,100}\',\'[^\']+\',\d{0,100},\'[^\']+\',\d{0,100},\d{0,100}\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_movement set proc = 1 where moveid = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT INTO s1_movement values \([^)]+\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT INTO s1_send values \([^)]+\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT INTO s1_general values \([^)]+\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (\*|owner|clay|iron|wood|crop) FROM s1_odata where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_wdata left JOIN s1_odata ON s1_odata.wref = s1_wdata.id where s1_wdata.id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+update s1_hero set (`)?(health|experience)(`)?( )?=( )?(`)?(health|experience)(`)?( )?[+-]( )?(-)?\d{0,100} where (`)?(heroid|uid)(`)?=\d{0,100}\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_enforcement set u\d{0,100} = u\d{0,100} [+-] \d{0,100} where id = \d{0,100}\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_(v|o)data WHERE loyalty<>100\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.ref = s1_attacks.id and s1_movement.proc = \'0\' and s1_movement.sort_type = \'\d{0,100}\'( and s1_attacks.attack_type = \'\d{0,100}\')? and endtime < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_send where s1_movement.ref = s1_send.id and s1_movement.proc = 0 and sort_type = \d{0,100} and endtime < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement where (ref = 0 and )?proc = (\')?0(\')? and sort_type = (\')?\d{0,100}(\')? and endtime < \d{0,100}(\.\d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_general WHERE shown = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users WHERE invited != 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_banlist WHERE active = 1 and end < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_odata where conqured = 0 and lastupdated2 < \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_config\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_artefacts where type = \d{0,100} and active = 1 and lastupdate <= \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement,s1_odata, s1_attacks where s1_odata.wref = \'\d{0,100}\' and s1_movement.to = \d{0,100} and s1_movement.ref = s1_attacks.id and s1_attacks.attack_type != \d{0,100} and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement where s1_movement.to = \'\d{0,100}\' and sort_type = \d{0,100} and ref = \d{0,100} and proc = 0 ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement, s1_attacks where s1_movement.(from|to) = (\')?\d{0,100}(\')? (and s1_movement.(from|to) = (\')?\d{0,100}(\')?)? and s1_movement.ref = s1_attacks.id and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} and (\()?s1_attacks.attack_type = \d{0,100}( or s1_attacks.attack_type = \d{0,100}\))? ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT a.wref, a.name, b.x, b.y from s1_vdata AS a left join s1_wdata AS b ON b.id = a.wref where owner = \d{0,100} order by capital DESC,pop DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM `s1_links` WHERE `userid` = \d{0,100} ORDER BY `pos` ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT into s1_enforcement \(vref,`from`\) values \(\d{0,100},\d{0,100}\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_enforcement set ((u-)?\d{0,100}|hero) = ((u-)?\d{0,100}|hero) [+-] \d{0,100} where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* from s1_enforcement where `from` = \d{0,100} and vref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+DELETE FROM s1_enforcement WHERE u1=0 AND u2=0 AND u3=0 AND u4=0 AND u5=0 AND u6=0 AND u7=0 AND u8=0 AND u9=0 AND u10=0 AND u11=0 AND u12=0 AND u13=0 AND u14=0 AND u15=0 AND u16=0 AND u17=0 AND u18=0 AND u19=0 AND u20=0 AND u21=0 AND u22=0 AND u23=0 AND u24=0 AND u25=0 AND u26=0 AND u27=0 AND u28=0 AND u29=0 AND u30=0 AND u31=0 AND u32=0 AND u33=0 AND u34=0 AND u35=0 AND u36=0 AND u37=0 AND u38=0 AND u39=0 AND u40=0 AND u41=0 AND u42=0 AND u43=0 AND u44=0 AND u45=0 AND u46=0 AND u47=0 AND u48=0 AND u49=0 AND u50=0 AND hero=0 AND \(vref=\d{0,100} OR `from`=\d{0,100}\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (wood|clay|iron|crop) FROM s1_vdata WHERE wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT sum\(s1_send.merchant\) from s1_send, s1_movement where s1_movement.from = \'\d{0,100}\' and s1_send.id = s1_movement.ref and s1_movement.proc = 0 and sort_type = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT sum\(ref\) from s1_movement where sort_type = \d{0,100} and s1_movement.to = \'\d{0,100}\' and proc = 0\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT (sum\(merchant\)|\*) from s1_market where vref = \d{0,100} and accept = \d{0,100}\n/i',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT oasistype,occupied FROM s1_wdata where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+DELETE FROM s1_training where id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_users SET village_select=\d{0,100} WHERE id=\d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_movement(, s1_send)? where s1_movement.(to|from) = \'\d{0,100}\'( and s1_movement.ref = s1_send.id)? and s1_movement.proc = 0 and s1_movement.sort_type = \d{0,100} ORDER BY endtime ASC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT vref FROM s1_fdata WHERE f99 = \'\d{0,100}\' and f99t = \'\d{0,100}\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+INSERT into s1_bdata values \([^)]+\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+Insert into s1_build_log values \([^)]+\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_prisoners where wref = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT\n[^A-Za-z]+s1_wdata.id AS map_id,\n[^A-Za-z]+s1_wdata.fieldtype AS map_fieldtype,\n[^A-Za-z]+s1_wdata.oasistype AS map_oasis,\n[^A-Za-z]+s1_wdata.x AS map_x,\n[^A-Za-z]+s1_wdata.y AS map_y,\n[^A-Za-z]+s1_wdata.occupied AS map_occupied,\n[^A-Za-z]+s1_wdata.image AS map_image,\n\n[^A-Za-z]+s1_odata.conqured AS oasis_conqured,\n[^A-Za-z]+info_user_oasis.username AS oasis_user,\n[^A-Za-z]+info_user_oasis.tribe AS oasis_tribe,\n[^A-Za-z]+info_alliance_oasis.tag AS oasis_alli_name,\n\n[^A-Za-z]+s1_vdata.wref AS ville_id,\n[^A-Za-z]+s1_vdata.owner AS ville_user,\n[^A-Za-z]+s1_vdata.name AS ville_name,\n[^A-Za-z]+s1_vdata.capital AS ville_capital,\n[^A-Za-z]+s1_vdata.pop AS ville_pop,\n\n[^A-Za-z]+s1_users.id AS user_id,\n[^A-Za-z]+s1_users.username AS user_username,\n[^A-Za-z]+s1_users.tribe AS user_tribe,\n[^A-Za-z]+s1_users.alliance AS user_alliance,\n\n[^A-Za-z]+s1_alidata.id AS aliance_id,\n[^A-Za-z]+s1_alidata.tag AS aliance_name\n\n[^A-Za-z]+FROM \(\(\(\(\(\(s1_wdata\n[^A-Za-z]+LEFT JOIN s1_vdata ON s1_vdata.wref = s1_wdata.id \)\n[^A-Za-z]+LEFT JOIN s1_odata ON s1_odata.wref = s1_wdata.id \)\n[^A-Za-z]+LEFT JOIN s1_users AS info_user_oasis ON info_user_oasis.id = s1_odata.owner \)\n[^A-Za-z]+LEFT JOIN s1_alidata AS info_alliance_oasis ON info_alliance_oasis.id = info_user_oasis.alliance \)\n[^A-Za-z]+LEFT JOIN s1_users ON s1_users.id = s1_vdata.owner \)\n[^A-Za-z]+LEFT JOIN s1_alidata ON s1_alidata.id = s1_users.alliance \)\n[^A-Za-z]+where s1_wdata.id IN \([^)]+\)\n[^A-Za-z]+ORDER BY FIND_IN_SET\(s1_wdata.id,\'[^\']+\'\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_diplomacy WHERE \(alli1 = \'[^\']*\' or alli2 = \'[^\']*\'\) AND \(type = \'\d{0,100}\' AND accepted = \'\d{0,100}\'\)\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_diplomacy WHERE alli1 = \'[^\']*\' AND type = \'\d{0,100}\' OR alli2 = \'[^\']*\' AND type = \'\d{0,100}\' AND accepted = \'\d{0,100}\'\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+UPDATE s1_general SET shown = \d{0,100} WHERE id = \d{0,100}\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT id,x,y,occupied(,fieldtype)? FROM s1_wdata WHERE fieldtype = \d{0,100}( OR fieldtype = \d{0,100})?\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT s1_users.id userid, s1_users.username username, s1_users.(a|d)pall,  \(\n\n[^A-Za-z]+SELECT COUNT\( s1_vdata.wref \)\n[^A-Za-z]+FROM s1_vdata\n[^A-Za-z]+WHERE s1_vdata.owner = userid AND type != \d{0,100}\n[^A-Za-z]+\)totalvillages, \(\n\n[^A-Za-z]+SELECT SUM\( s1_vdata.pop \)\n[^A-Za-z]+FROM s1_vdata\n[^A-Za-z]+WHERE s1_vdata.owner = userid\n[^A-Za-z]+\)pop\n[^A-Za-z]+FROM s1_users\n[^A-Za-z]+WHERE s1_users.(a|d)pall >=0 AND s1_users.access < 8 AND s1_users.tribe <= 3\n[^A-Za-z]+AND s1_users.id > 5\n[^A-Za-z]+ORDER BY s1_users.(a|d)pall DESC, pop DESC, userid DESC\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT v.wref,v.name,v.owner,v.pop FROM s1_vdata AS v,s1_users AS u WHERE v.owner=u.id AND u.tribe<=3 AND v.wref != \'[^\']*\' AND u.access<8\n/',
        '/(\d{0,100} \d{1,2}:\d{1,2}:\d{1,2})?[ \t]+\d{1,100}[ \t]+Query[ \t]+SELECT \* FROM s1_users WHERE tribe!=0 AND tribe!=4 AND tribe!=5\n/',
    ];
    
    echo preg_replace($regexes, '', $src);
?>