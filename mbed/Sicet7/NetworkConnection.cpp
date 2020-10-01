#include "mbed.h"
#include <Sicet7/NetworkConnection.h>
#include "EthernetInterface.h"

namespace Sicet7 {
    
    NetworkConnection::NetworkConnection() {
        this->connected = false;
        this->interface = new EthernetInterface();
        this->connect();
    }
    
    bool NetworkConnection::isConnected() {
        return this->connected;
    }
    
    NetworkInterface* NetworkConnection::getInterface() {
        return this->interface;
    }
    
    const char* NetworkConnection::getIpAddress() {
        if (!this->isConnected()) {
            return "0.0.0.0";
        }
        return this->getInterface()->get_ip_address();
    }
    
    void NetworkConnection::connect() {
        if(!this->isConnected()) {
            nsapi_error_t connect_status = this->getInterface()->connect();
            if (connect_status == NSAPI_ERROR_OK) {
                this->connected = true;
            }
        }
    }
    
}