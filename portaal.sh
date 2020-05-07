#!/bin/bash

#variables
BASE_DIR="/home/vagrant/VM2/"
KLANT=""
OMGEVING=""
HOST="web01"

#functions
bestaandeomgeving() {
	clear
	PS3="Kies een omgeving: "
	select omgeving in $(find $BASE_DIR/klanten/$klant -mindepth 1 -maxdepth 1 -type d | xargs -n 1 basename); do
		OMGEVING=$omgeving
		break
	done;
}

nieuweomgeving() {
	clear
	read -p "Omgevingnaam: " omgeving
	OMGEVING=$omgeving
	cd $BASE_DIR/klanten/$KLANT/
	cp -R --preserve=mode $BASE_DIR/templates/voorbeeld_klant/voorbeeld_omgeving/ $OMGEVING
	cd $OMGEVING
	ssh-keygen -q -f $KLANT-$OMGEVING-id_rsa -N ""
	sed -i -e "s;%klant%;$KLANT;g" -e "s;%omgeving%;$OMGEVING;g" ansible.cfg
	sed -i -e "s;%klant%;$KLANT;g" -e "s;%omgeving%;$OMGEVING;g" -e "s;%host%;$HOST;g" hosts
	sed -i -e "s;%klant%;$KLANT;g" -e "s;%omgeving%;$OMGEVING;g" -e "s;%host%;$HOST;g" Vagrantfile
}

bestaandeklant() {
	clear
	PS3="Kies een klant: "
	select klant in $(find $BASE_DIR/klanten/ -mindepth 1 -maxdepth 1 -type d | xargs -n 1 basename); do
		KLANT=$klant
		break
	done;
}

nieuweklant() {
	clear
	read -p "Klantnaam: " klant
	KLANT=$klant
	cd $BASE_DIR/klanten/
	mkdir $KLANT
	nieuweomgeving
}

kiesomgeving() {
	clear
	PS3="Kies een optie: "
	select omgevingstatus in "Bestaande omgeving" "Nieuwe omgeving"; do
		case $omgevingstatus in
			"Bestaande omgeving" )
				bestaandeomgeving;;
			"Nieuwe omgeving" )
				nieuweomgeving;;
		esac
		break
	done;
}

verwijderomgeving() {
	#voeg extra commando's toe voor het destroyen van vagrant machines enz
	cd $BASE_DIR/klanten/$KLANT/
	rm -rf $OMGEVING
}

verwijderklant() {
	verwijderomgeving
	cd $BASE_DIR/klanten/
	rm -rf $KLANT
}

openportaal() {
	clear
	select optie in "vagrant up" "vagrant halt" "vagrant destroy" "ansible-playbook playbook.yml" "verwijder omgeving"; do
		cd $BASE_DIR/klanten/$KLANT/$OMGEVING/
		case $optie in
			"vagrant up" )
				vagrant up;;
			"vagrant halt" )
				vagrant halt;;
			"vagrant destroy" )
				vagrant destroy;;
			"ansible-playbook playbook.yml" )
				ansible-playbook playbook.yml;;
			"verwijder omgeving" )
				verwijderomgeving;;
		esac
		break
	done;
}

#start of script
clear
# shopt -s dotglob
# shopt -s nullglob
PS3="Kies een optie: "
select klantstatus in "Bestaande klant" "Nieuwe klant" "Verwijder klant"; do
	case $klantstatus in
		"Bestaande klant" )
			bestaandeklant;;
		"Nieuwe klant" )
			nieuweklant;;
		"Verwijder klant" )
			verwijderklant;;
	esac
	kiesomgeving
	openportaal
	break
done
