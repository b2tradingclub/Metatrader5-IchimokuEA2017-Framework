//+------------------------------------------------------------------+
//|                                     IchimokuExperimental002-2JCS.mq5 |
//|                                   Copyright 2017, Trader77330@NetCourrier.Com|
//|                                   https://ichimoku-expert.blogspot.com |
//+------------------------------------------------------------------+

//IchimokuExperimental002-2JCS.mq5 : Scans for Price Equals SSB on Current Time Frame

//Si bougie actuelle est une nouvelle bougie

//Et Si LS>SSA et LS>SSB et LS>TS et LS>KS

//Et Si SSA>SSB
//Et Si bougie n-2 passe au-dessus de SSA    OU    bougie n-2 est sous SSB
//Et bougie n-1 est au-dessus de SSA
//Sur l'unité de temps en cours

//Ou Si SSB>SSA
//Si bougie n-2 passe au-dessus de SSB    OU    bougie n-2 est sous SSB
//Et Et bougie n-1 est au-dessus de SSB
//Sur l'unité de temps en cours


//notif:android mt5:327C822F

#property copyright "Copyright 2017, Investdata Systems"
#property link      "https://ichimoku-expert.blogspot.com"
#property version   "1.01"
//+------------------------------------------------------------------+
//| Expert initialization function                                   |
//+------------------------------------------------------------------+

#include <Trade\Trade.mqh>
#include <Trade\AccountInfo.mqh>
#include <Trade\PositionInfo.mqh>

CAccountInfo accountInfo;
double initialEquity = 0;
double currentEquity = 0;

input bool exportPrices=false;
int file_handle=INVALID_HANDLE; // File handle
input int scanPeriod=30;
input bool onlySymbolsInMarketwatch=true;
input string symbolToIgnore="EURCZK";
// TODO : Gérer plusieurs symboles séparés par des virgules

string appVersion="2JCSxSSAorSSB-CTF";
string versionInfo="Scans 2 last candlesticks with SSA/SSB on current timeframe ("+EnumToString(Period())+")";
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
int OnInit()
  {
   MqlDateTime mqd;
   TimeCurrent(mqd);
   string timestamp=string(mqd.year)+"-"+IntegerToString(mqd.mon,2,'0')+"-"+IntegerToString(mqd.day,2,'0')+" "+IntegerToString(mqd.hour,2,'0')+":"+IntegerToString(mqd.min,2,'0')+":"+IntegerToString(mqd.sec,2,'0');

   string output="";
   output = timestamp + " Starting " + StringSubstr(__FILE__,0,StringLen(__FILE__)-4) + " " + appVersion + " Investdata Systems";
   output = output + " Version info : " + versionInfo;
   output = output + " https://ichimoku-ea.000webhostapp.com/";
   printf(output);
   SendNotification(output);

   if(exportPrices)
     {
      printf("exportDir = "+TerminalInfoString(TERMINAL_COMMONDATA_PATH));
     }

   ObjectsDeleteAll(0,"",-1,-1);

   EventSetTimer(scanPeriod); // 30 secondes pour tout (pas seulement marketwatch)

   initialEquity=accountInfo.Equity();

   if(exportPrices)
     {
      //--- Create file to write data in the common folder of the terminal
      //C:\Users\Idjed\AppData\Roaming\MetaQuotes\Terminal\Common\Files
      MqlDateTime mqd;
      TimeCurrent(mqd);
      string timestamp=string(mqd.year)+IntegerToString(mqd.mon,2,'0')+IntegerToString(mqd.day,2,'0')+IntegerToString(mqd.hour,2,'0')+IntegerToString(mqd.min,2,'0')+IntegerToString(mqd.sec,2,'0');

      file_handle=FileOpen(timestamp+"_backup.csv",FILE_CSV|FILE_WRITE|FILE_ANSI|FILE_COMMON);
      if(file_handle>0)
        {
         FileWrite(file_handle,"Timestamp;Name;Period;Buy;Sell;Tenkan;Kijun;Chikou(t-26);SSA;SSB");
        }
     }

   return(INIT_SUCCEEDED);
  }
//+------------------------------------------------------------------+
//| Expert deinitialization function                                 |
//+------------------------------------------------------------------+
void OnDeinit(const int reason)
  {

   if(exportPrices)
     {
      //--- Close the file
      FileClose(file_handle);
     }

   EventKillTimer();

  }
//+------------------------------------------------------------------+
//| Expert tick function                                             |
//+------------------------------------------------------------------+
void OnTick()
  {
   //Ichimoku();

   /*MqlDateTime mqd;
   TimeCurrent(mqd);
   
   MqlTick lasttick;
   SymbolInfoTick(Symbol(),lasttick);
   double price= lasttick.ask;
   double sell = lasttick.bid; double buy = lasttick.ask; double spread = buy-sell;
   ulong vol=lasttick.volume;*/
   
   //string timestamp=string(mqd.year)+IntegerToString(mqd.mon,2,'0')+IntegerToString(mqd.day,2,'0')+IntegerToString(mqd.hour,2,'0')+IntegerToString(mqd.min,2,'0')+IntegerToString(mqd.sec,2,'0');
   //timestamp+=IntegerToString(GetTickCount());
   
   //uploadHistory(timestamp, Symbol(), buy, sell);   
   
   stotal=SymbolsTotal(onlySymbolsInMarketwatch); // seulement les symboles dans le marketwatch (false)
   for(int sindex=0; sindex<stotal; sindex++)
   {
      MqlDateTime mqd;
      TimeCurrent(mqd);

      string timestamp=string(mqd.year)+IntegerToString(mqd.mon,2,'0')+IntegerToString(mqd.day,2,'0')+IntegerToString(mqd.hour,2,'0')+IntegerToString(mqd.min,2,'0')+IntegerToString(mqd.sec,2,'0');
      timestamp+=IntegerToString(GetTickCount());
      string sname=SymbolName(sindex,onlySymbolsInMarketwatch);
   
      MqlTick lasttick;
      SymbolInfoTick(sname,lasttick);
      double price= lasttick.ask;
      double sell = lasttick.bid; double buy = lasttick.ask; double spread = buy-sell;
      ulong vol=lasttick.volume;

      uploadHistory(timestamp, sname, buy, sell);      
   }
   
   return;
  }

//+------------------------------------------------------------------+
//| Timer function                                                   |
//+------------------------------------------------------------------+
datetime allowed_until=D'2017.12.15 00:00';
bool expiration_notified=false;
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void OnTimer()
  {
   if(TimeCurrent()>allowed_until)
     {
      if(expiration_notified==false)
        {
         string output=StringSubstr(__FILE__,0,StringLen(__FILE__)-4)+" "+appVersion+" : EXPIRED. Please contact Investdata Systems (+33)787817434";
         printf(output);
         SendNotification(output);
         expiration_notified=true;
        }
      return;
     }

   Ichimoku();

//currentEquity = accountInfo.Equity();
//double deltaEquity = currentEquity-initialEquity;
//printf("currentEquity-initialEquity=" + string(deltaEquity));
//SendNotification("currentEquity-initialEquity=" + deltaEquity);
  }

bool first_run_done[];

static int BARS[];

int maxhisto=64;

bool initdone=false;
int stotal=0;
//bool onlySymbolsInMarketwatch=true;
//datetime allowed_until = D'2016.01.15 00:00';
//bool expiration_notified = false;
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void Ichimoku()
  {
                        //timestamp+=IntegerToString(GetTickCount());
                        //upload2JCSAlert(timestamp, EnumToString(Period()), sname, buy, sell, (string)checkPeriod(PERIOD_H1));                       
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
bool checkPeriod(ENUM_TIMEFRAMES period)
  {
   return result;
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void CloseAllPositions()
  {
   CTrade trade;
   int i=PositionsTotal()-1;
   while(i>=0)
     {
      if(trade.PositionClose(PositionGetSymbol(i))) i--;
     }
  }
//+------------------------------------------------------------------+
//| Trade function                                                   |
//+------------------------------------------------------------------+
void OnTrade()
  {
//---

  }
//+------------------------------------------------------------------+
//| TradeTransaction function                                        |
//+------------------------------------------------------------------+
void OnTradeTransaction(const MqlTradeTransaction &trans,
                        const MqlTradeRequest &request,
                        const MqlTradeResult &result)
  {
//---

  }
//+------------------------------------------------------------------+
//| Tester function                                                  |
//+------------------------------------------------------------------+
double OnTester()
  {
   double ret=0.0;
   return(ret);
  }
//+------------------------------------------------------------------+
//| TesterInit function                                              |
//+------------------------------------------------------------------+
void OnTesterInit()
  {
  }
//+------------------------------------------------------------------+
//| TesterPass function                                              |
//+------------------------------------------------------------------+
void OnTesterPass()
  {
  }
//+------------------------------------------------------------------+
//| TesterDeinit function                                            |
//+------------------------------------------------------------------+
void OnTesterDeinit()
  {
  }
//+------------------------------------------------------------------+
//| ChartEvent function                                              |
//+------------------------------------------------------------------+
void OnChartEvent(const int id,
                  const long &lparam,
                  const double &dparam,
                  const string &sparam)
  {
  }
//+------------------------------------------------------------------+
//| BookEvent function                                               |
//+------------------------------------------------------------------+
void OnBookEvent(const string &symbol)
  {
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
bool disableUploadHistory = true;
void uploadHistory(string timestamp,string name,double buy,double sell)
  {
   if (disableUploadHistory == true) return;

// "https://ichimoku-ea.000webhostapp.com/ichimoku-ea-v2/?upload_history="
   string cookie=NULL,headers;
   char post[],result[];

   string history = timestamp + ";" + name + ";" + DoubleToString(buy) + ";" + DoubleToString(sell);

   string google_url="https://ichimoku-ea.000webhostapp.com/ichimoku-ea-v2/?upload_history="+history;
   int timeout=5000; //--- Timeout below 1000 (1 sec.) is not enough for slow Internet connection 
   int res=WebRequest("GET",google_url,cookie,NULL,timeout,post,0,result,headers);
   if(res==-1)
     {
      Print("Error in WebRequest. Error code  =",GetLastError());
      //--- Perhaps the URL is not listed, display a message about the necessity to add the address 
      //MessageBox("Add the address '"+google_url+"' in the list of allowed URLs on tab 'Expert Advisors'","Error",MB_ICONINFORMATION); 
     }
   else
     {
      //printf(CharArrayToString(result));
      //--- Load successfully 
      //PrintFormat("The file has been successfully loaded, File size =%d bytes.",ArraySize(result)); 
      //printf("History sent successfully");
     }
  //disableUploadHistory = true;
  }

//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void upload2JCSAlert(string timestamp,string period,string symbol,double buy,double sell,string h1_ls_validated)
  {
// "https://ichimoku-ea.000webhostapp.com/ichimoku-ea-v2/?upload_2jcs_alert=test"
   string cookie=NULL,headers;
   char post[],result[];

   string jcsalert=timestamp+";"+period+";"+symbol+";"+DoubleToString(buy)+";"+DoubleToString(sell)+";"+h1_ls_validated;

   string google_url="https://ichimoku-ea.000webhostapp.com/ichimoku-ea-v2/?upload_2jcs_alert="+jcsalert;
   int timeout=5000; //--- Timeout below 1000 (1 sec.) is not enough for slow Internet connection 
   int res=WebRequest("GET",google_url,cookie,NULL,timeout,post,0,result,headers);
   if(res==-1)
     {
      Print("Error in WebRequest. Error code  =",GetLastError());
      //--- Perhaps the URL is not listed, display a message about the necessity to add the address 
      //MessageBox("Add the address '"+google_url+"' in the list of allowed URLs on tab 'Expert Advisors'","Error",MB_ICONINFORMATION); 
     }
   else
     {
      //printf(CharArrayToString(result));
      //--- Load successfully 
      //PrintFormat("The file has been successfully loaded, File size =%d bytes.",ArraySize(result)); 
      printf("2JCS Alert sent successfully");
     }
  }


//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void uploadSSBAlert(string timestamp,string period,string name,string type,double price,double ssb)
  {
// "https://ichimoku-ea.000webhostapp.com/?notification=test"
   string cookie=NULL,headers;
   char post[],result[];

   string ssbalert=timestamp+";"+period+";"+name+";"+type+";"+DoubleToString(price)+";"+DoubleToString(ssb);

   string google_url="https://ichimoku-ea.000webhostapp.com/?upload_ssb_alert="+ssbalert;
   int timeout=5000; //--- Timeout below 1000 (1 sec.) is not enough for slow Internet connection 
   int res=WebRequest("GET",google_url,cookie,NULL,timeout,post,0,result,headers);
   if(res==-1)
     {
      Print("Error in WebRequest. Error code  =",GetLastError());
      //--- Perhaps the URL is not listed, display a message about the necessity to add the address 
      //MessageBox("Add the address '"+google_url+"' in the list of allowed URLs on tab 'Expert Advisors'","Error",MB_ICONINFORMATION); 
     }
   else
     {
      //printf(CharArrayToString(result));
      //--- Load successfully 
      //PrintFormat("The file has been successfully loaded, File size =%d bytes.",ArraySize(result)); 
      printf("SSB Alert sent successfully");
     }
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void resetAllRemoteData()
  {
// "https://ichimoku-ea.000webhostapp.com/?notification=test"
   string cookie=NULL,headers;
   char post[],result[];
   string google_url="https://ichimoku-ea.000webhostapp.com/?reset_all=true";
   int timeout=5000; //--- Timeout below 1000 (1 sec.) is not enough for slow Internet connection 
   int res=WebRequest("GET",google_url,cookie,NULL,timeout,post,0,result,headers);
   if(res==-1)
     {
      Print("Error in WebRequest. Error code  =",GetLastError());
      //--- Perhaps the URL is not listed, display a message about the necessity to add the address 
      //MessageBox("Add the address '"+google_url+"' in the list of allowed URLs on tab 'Expert Advisors'","Error",MB_ICONINFORMATION); 
     }
   else
     {
      printf(CharArrayToString(result));
      //--- Load successfully 
      //PrintFormat("The file has been successfully loaded, File size =%d bytes.",ArraySize(result)); 
      printf("Reset command sent successfully");
     }
  }
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void resetSSBAlertsRemoteData()
  {
// "https://ichimoku-ea.000webhostapp.com/?notification=test"
   string cookie=NULL,headers;
   char post[],result[];
   string google_url="https://ichimoku-ea.000webhostapp.com/?reset_ssb_alerts=true";
   int timeout=5000; //--- Timeout below 1000 (1 sec.) is not enough for slow Internet connection 
   int res=WebRequest("GET",google_url,cookie,NULL,timeout,post,0,result,headers);
   if(res==-1)
     {
      Print("Error in WebRequest. Error code  =",GetLastError());
      //--- Perhaps the URL is not listed, display a message about the necessity to add the address 
      //MessageBox("Add the address '"+google_url+"' in the list of allowed URLs on tab 'Expert Advisors'","Error",MB_ICONINFORMATION); 
     }
   else
     {
      printf(CharArrayToString(result));
      //--- Load successfully 
      //PrintFormat("The file has been successfully loaded, File size =%d bytes.",ArraySize(result)); 
      printf("Reset command sent successfully");
     }
  }
//+------------------------------------------------------------------+
