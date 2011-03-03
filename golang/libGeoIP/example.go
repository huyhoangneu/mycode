package main

import (
	"fmt"
	"flag"
	"libgeo"
)

func main() {
	flag.Parse()

	// Check the number of arguments
	if flag.NArg() < 2 {
		fmt.Printf("usage: main DBFILE IPADDRESS\n");
		return
	}

	// Set the arguments
	dbFile := flag.Arg(0)
	ipAddr := flag.Arg(1)

	// Load the database file, exit on failure
	gi := libgeo.Load(dbFile)
	if gi == nil {
		fmt.Printf("GI IS NULL!\n");
		return
	}

	// Lookup the IP and display the details if country is found
	loc := gi.GetLocationByIP(ipAddr)
	if loc != nil {
		fmt.Printf("Country: %s (%s)\n", loc.CountryName, loc.CountryCode)
		fmt.Printf("City: %s\n", loc.City)
		fmt.Printf("Region: %s\n", loc.Region)
		fmt.Printf("Postal Code: %s\n", loc.PostalCode)
		fmt.Printf("Latitude: %f\n", loc.Latitude)
		fmt.Printf("Longitude: %f\n", loc.Longitude)
	}
}