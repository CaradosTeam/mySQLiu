#!/bin/bash
alias mysqliu="mysqliu.sh"
sudo chmod a+x mysqliu.sh
mkpasswd -V >/dev/null || { echo >&2 "Mysqliu require whois but it's not installed. Instaling..."; sudo apt install whois; exit 1; }

declare -r errorUnknownCommand="\e[31mUnknown command, if you need assistance, type \e[1mhelp\e[0m\e[31m, \e[1mman mysqliu\e[0m\e[31m or \e[1mmysqliu -h\e[0m\n"
declare -r listOfCommands="//////////////////////////////\n/////// \e[1mCommand List\e[0m ////////\n////////////////////////////\nexit - quit mysqliu\n"
declare -r listOfCommandsServer="/////////////////////////////////\n///// \e[1mHTTP Server Commands\e[0m //////\n///////////////////////////////\nserver start - start php server with panel\nserver stop - server terminate\nserver info - server details\n"

main() {
cliSign="mysqliu> "
while read -p "$cliSign" command
do
    case "$command" in
    "help") echo -e "$listOfCommands" ;;
    "login") loginMysql ;;
    "autologin") loginMysqlCnf ;;
    "config autologin") configureLoginCnf ;;
    "clear") clear ;;
    "exit") exit 1 ;;
    *) echo -e "$errorUnknownCommand"
    esac
done
}

loginMysql() {
    if [ -z $1 ] ;
    then
    echo "Type a login"
    read login
    else
    login="$1"
    fi
    echo "Type a password"
    read -s password

#mysql_config_editor set --login-path=local --host=localhost --user="$login" --password="$passwords"
#sudo mysql -u "$login" -p"$password" -e "SELECT USER(),CURRENT_USER();"
#mysql --login-path=local  -e "SELECT USER(),CURRENT_USER();"  | tail -n +2
    if sudo mysql -u "$login" -p"$password" -e "SELECT USER() AS Username" ;
    then
    echo "Hi $login!"
    onLogin "manual"
    fi

    return 1
}

configureLoginCnf() {
    if [ -z $1 ] ;
    then
    echo "Type a login"
    read login
    else
    login="$1"
    fi
    echo "Type a password"
    read -s password
sudo cat > autologin.cnf <<==ENDOFFILE
[client]
user = $login
password = $password
host = localhost
==ENDOFFILE
    clear
    echo "Data Updated"
}

loginMysqlCnf() {
    if [ -e "autologin.cnf" ] ;
    then
login=$(sed -e '2q;d' autologin.cnf)
#login=$(cut -d "=" -f 2 <<< "$login")
login=$(echo $login | grep -oP '(?<=\=\s).*$')
        if mysql --defaults-extra-file=autologin.cnf -e "SELECT USER() AS Username";
        then
        echo "Hi $login!"
        onLogin "auto"
        fi
    else
    configureLoginCnf
    fi
}

onLogin() {
    cliSign="mysqliu\$$login> "
    while read -a logcomm -p "$cliSign"
    do
        howParams="${#logcomm[@]}"
        paramZero="${logcomm[0]}"

        case "$paramZero" in
        "help") echo -e "$listOfCommands" ;;
        "server") if [ $howParams -gt 1 ]; then
            case "${logcomm[1]}" in
            "start") startPHPServerMysqliu ;;
            "stop") stopPHPServerMysqliu ;;
            "terminate") killall -9 php ;;
            "locate") startPHPServerMysqliu ;;
            "info") startPHPServerMysqliu ;;
            *) echo -e "$errorUnknownCommand"
            esac
        else 
        echo -e "$listOfCommandsServer"
        fi ;;
        "logout") onLogout ;;
        "find") if [ $howParams -gt 1 ]; then
            echo "found"
        else 
        echo -e "find {{searchkeyword}} -t [tables] -d [databases]"
        fi ;;
        "query") if [ $howParams -gt 1 ]; then
        queries=( $(echo ${logcomm[@]} | grep -oP '".*?"' ) ) #(?<=\s)\".*\"') ".*+ [^"]+
        echo "${queries[@]}"
        queryMysql $1 "${queries[@]}"
        else
        echo -e "\e[31mYou must specify a query\e[0m"
        fi
        ;;
        "clear") clear ;;
        "exit") exit 1 ;;
        *) echo -e "$errorUnknownCommand"
        esac
    done
}

onLogout() {
    if [ ! -z $login ] ;
    then
    unset $login
    fi
    if [ ! -z $password ] ;
    then
    unset $password
    fi

    clear
    echo "Logout Successfull"
    main
    return 1
}

queryMysql() {
    temp="${*:2}"
    temp="${temp%\"}"
    temp="${temp#\"}"
    if [ "$1" = "auto" ] ;
    then
    sudo mysql --defaults-extra-file=autologin.cnf -e "$2"
    else 
    sudo mysql -u $login -p$password -e "$temp"
    fi
}

#Mysqliu Server Admin
startPHPServerMysqliu() {
    {
    php -S localhost:3308 -t ./httpserver/php ./httpserver/php/router.php
    } > ./httpserver/php/logs/runtime.log 2>&1 &
    if ps aux | grep 'php -S localhost:3308 -t ./httpserver/php ./httpserver/php/router.php' | awk '{print $2}'; then
    datetimeFormated=`date +"%a %b  %d %T %Y"`
    echo "[$datetimeFormated] Server Started Successfully on localhost:3308" > ./httpserver/php/logs/runtime.log
    #| awk '{if(NR!=4)print}'
    firefox "localhost:3308" >/dev/null 2>&1 &
    echo "Server Started Successfully, to stop type server stop, for information server info and to open page in browser server locate"
    else
    echo "Server Got Error, configuration makes server run uncomplite"
    fi
    return 1
}

stopPHPServerMysqliu() {
    kill $(ps aux | grep 'php -S localhost:3308 -t ./httpserver/php ./httpserver/php/router.php' | awk '{print $2}')
    #killall -9 php php -S localhost:3308 -t ./httpserver/php ./httpserver/php/router.php
}

if [ -z "$1" ] ;
then
echo -e "MySQLiu engine\nVersion: \e[32m0.1 Beta\e[0m\nFor help type \e[1mhelp\e[0m, \e[1mman mysqliu\e[0m or \e[1mmysqliu -h\e[0m"
    main
else
    case "$1" in
    "-V") echo -e "\e[32m0.1 Beta\e[0m , Compilation 1" ;;
    "-h") echo -e "$listOfCommands" ;;
    "-u") if [ -z $2 ]
    then 
    echo -e "\e[31mYou must specify a name of user\e[0m" 
    else
    loginMysql $2
    fi 
    ;;
    "-ca") if [ -z $2 ]
    then 
    echo -e "\e[31mYou must specify a name of user\e[0m" 
    else
    configureLoginCnf $2
    fi 
    ;;
    *) echo -e "$errorUnknownCommand"
    esac
fi