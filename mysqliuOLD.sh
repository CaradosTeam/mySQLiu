#!/bin/bash
alias mysqliu="mysqliu.sh"
sudo chmod a+x mysqliu.sh
mkpasswd -V >/dev/null || { echo >&2 "Mysqliu require whois but it's not installed. Instaling..."; sudo apt install whois; exit 1; }

declare -r errorUnknownCommand="\e[31mUnknown command, if you need assistance, type \e[1mhelp\e[0m\e[31m, \e[1mman mysqliu\e[0m\e[31m or \e[1mmysqliu -h\e[0m\n"
declare -r listOfCommands="//////////////////////////////\n/////// \e[1mCommand List\e[0m ////////\n////////////////////////////\nexit - quit mysqliu\n"

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
    onLogin "login" $login $password
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
sudo cat > config.cnf <<==ENDOFFILE
[client]
user = $login
password = $password
host = localhost
==ENDOFFILE
    clear
    echo "Data Updated"
}

loginMysqlCnf() {
    if [ -e "config.cnf" ] ;
    then
login=$(sed -e '2q;d' config.cnf)
login=$(cut -d "=" -f 2 <<< "$test")
        if mysql --defaults-extra-file=config.cnf -e "SELECT USER() AS Username";
        then
        echo "Hi $login!"
        onLogin "auto" "$login"
        fi
    else
    configureLoginCnf
    fi
}

onLogin() {
    cliSign="mysqliu\$$2> "
    while read -a logcomm -p "$cliSign" command
    do
        howParams="${#logcomm[@]}"
        paramZero="${logcomm[0]}"

        echo "$paramZero"

        case "$paramZero" in
        "help") echo -e "$listOfCommands" ;;
        "logout") onLogout ;;
        "query") 
        if [ "$(expr $howParams)" -qt 1 ]; then 
        echo "$howParams"
        queryMysql $1 "${logcomm[1]}" 
        else
        echo -e "You must specify a query"
        fi
        ;;
        *) echo -e "$errorUnknownCommand"
        esac
    done
}

onLogout() {
    if [ !-z $login ] ;
    then
    unset $login
    fi
    if [ !-z $password ] ;
    then
    unset $password
    fi

    main

    echo "Logout Succesfull"
    return 1
}

queryMysql() {
    if [ "$1" -eq "auto" ] ;
    then
    sudo mysql --defaults-extra-file=config.cnf -e "$2"
    else 
    sudo mysql -u $login -p$password -e "$2"
    fi
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