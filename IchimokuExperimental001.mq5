//+------------------------------------------------------------------+
//|                                     Ichimoku2017_EA_PEqSSB-CTF.mq5 |
//|                                   Copyright 2017, Trader77330@NetCourrier.Com|
//|                                   https://traderetgagner.blogspot.com |
//+------------------------------------------------------------------+

//Ichimoku2017_EA_PEqSSB-CTF.mq5 : Scans for Price Equals SSB on Current Time Frame

//notif:android mt5,mt4=DB4F3016,EEF637E9,997CD24C,E0358708,96ABD519,B22E3F84
//contient aussi le code pour dumper les données ichimoku vers csv

#property copyright "Copyright 2017, Trader77330@NetCourrier.com"
#property link      "https://traderetgagner.blogspot.com"
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

input bool exportPrices = false;
int file_handle = INVALID_HANDLE; // File handle
input int scanPeriod = 20;
input bool onlySymbolsInMarketwatch = true;
input string symbolToIgnore = "EURCZK";
// TODO : Gérer plusieurs symboles séparés par des virgules

string appVersion = "PEqSSB-CTF";
string versionInfo = "This version scans for price == SSB on current timeframe (" + EnumToString(Period()) + ")";
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
int OnInit()
  {
   MqlDateTime mqd;
   TimeCurrent(mqd);
   string timestamp = string(mqd.year) + "-" + IntegerToString(mqd.mon,2,'0') + "-" + IntegerToString(mqd.day,2,'0')+ " " + IntegerToString(mqd.hour,2,'0') + ":" + IntegerToString(mqd.min,2,'0') + ":" + IntegerToString(mqd.sec,2,'0');

   string output = "";
   output = timestamp + " Starting Ichimoku EA 2017 " + appVersion + " Trader77330@NetCourrier.Com";
   output = output + " Version info : " + versionInfo;
   output = output + " https://ichimoku-ea.000webhostapp.com/";
   printf(output);
   SendNotification(output);
   //resetAllRemoteData();
   output = "Version info : " + versionInfo;
   printf(output);
   SendNotification(output);
   //output = "exportPrices = " + exportPrices;
   //printf(output);
   //SendNotification(output);
   if(exportPrices)
     {
      printf("exportDir = "+TerminalInfoString(TERMINAL_COMMONDATA_PATH));
     }

   ObjectsDeleteAll(0,"",-1,-1);
   //CloseAllPositions();
   //--- create timer
   EventSetTimer(scanPeriod); // 30 secondes pour tout (pas seulement marketwatch)

   initialEquity = accountInfo.Equity();
   //ReadLinearRegressionChannelData();

   if(exportPrices)
     {
      //--- Create file to write data in the common folder of the terminal
      //C:\Users\Idjed\AppData\Roaming\MetaQuotes\Terminal\Common\Files
      MqlDateTime mqd;
      TimeCurrent(mqd);
      string timestamp = string(mqd.year) + IntegerToString(mqd.mon,2,'0') + IntegerToString(mqd.day,2,'0') + IntegerToString(mqd.hour,2,'0') + IntegerToString(mqd.min,2,'0') + IntegerToString(mqd.sec,2,'0');

      file_handle = FileOpen(timestamp+"_backup.csv", FILE_CSV|FILE_WRITE|FILE_ANSI|FILE_COMMON);
      if(file_handle>0)
        {
         FileWrite(file_handle, "Timestamp;Name;Period;Buy;Sell;Tenkan;Kijun;Chikou(t-26);SSA;SSB");
        }
     }

   return(INIT_SUCCEEDED);
  }
//+------------------------------------------------------------------+
//| Expert deinitialization function                                 |
//+------------------------------------------------------------------+
void OnDeinit(const int reason)
  {
//CloseAllPositions();

   if(exportPrices)
     {
      //--- Close the file
      FileClose(file_handle);
     }

//--- destroy timer
   EventKillTimer();

  }
//+------------------------------------------------------------------+
//| Expert tick function                                             |
//+------------------------------------------------------------------+

//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void OnTick()
  {
  //Ichimoku();

   return;

   MqlTick lasttick;
   SymbolInfoTick(Symbol(),lasttick);
   double sell=lasttick.bid, buy=lasttick.ask, spread=buy-sell; 
   ulong vol=lasttick.volume;
   //printf("sell="+string(sell)+" ; buy="+string(buy)+ " ; spread="+string(spread) + " ; vol="+string(vol));
   
  }
  
//+------------------------------------------------------------------+
//| Timer function                                                   |
//+------------------------------------------------------------------+
datetime allowed_until = D'2017.12.15 00:00';
bool expiration_notified = false;
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void OnTimer()
  {
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
   if(TimeCurrent()>allowed_until)
     {
      if(expiration_notified==false)
        {
         string output = "Ichimoku EA 2017 " + appVersion + " : EXPIRED. Please contact Trader77330@NetCourrier.com (+33)787817434";
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

int maxhisto = 256;

bool initdone = false;
int stotal = 0;
//bool onlySymbolsInMarketwatch=true;
//datetime allowed_until = D'2016.01.15 00:00';
//bool expiration_notified = false;
//+------------------------------------------------------------------+
//|                                                                  |
//+------------------------------------------------------------------+
void Ichimoku()
  {
   int tenkan_sen = 9;              // period of Tenkan-sen
   int kijun_sen = 26;              // period of Kijun-sen
   int senkou_span_b = 52;          // period of Senkou Span B

//--- indicator buffer
   double tenkan_sen_buffer[];
   double kijun_sen_buffer[];
   double senkou_span_a_buffer[];
   double senkou_span_b_buffer[];
   double chikou_span_buffer[];

   ArraySetAsSeries(tenkan_sen_buffer, true);
   ArraySetAsSeries(kijun_sen_buffer, true);
   ArraySetAsSeries(senkou_span_a_buffer, true);
   ArraySetAsSeries(senkou_span_b_buffer, true);
   ArraySetAsSeries(chikou_span_buffer, true);

   if(!initdone)
     {
      stotal = SymbolsTotal(onlySymbolsInMarketwatch); // seulement les symboles dans le marketwatch (false)

      //nouveaux traitements ssb ks
      ArrayResize(first_run_done, stotal, stotal);
      //----

      //initialisation de tout le tableau à false car sinon la première valeur vaut true par défaut (bug?).
      for(int sindex=0; sindex<stotal; sindex++)
        {
         first_run_done[sindex] = false;
        }

      initdone = true;
     }
     
   int processingStart = GetTickCount();
   printf("Processing start = " + IntegerToString(processingStart));

   for(int sindex=0; sindex<stotal; sindex++)
     {

      string sname = SymbolName(sindex, onlySymbolsInMarketwatch);

      if(sname == symbolToIgnore)
        {
         printf(StringSubstr(__FILE__,0,StringLen(__FILE__)-4) + "(" + EnumToString(Period()) + ") : Ignoring = " + sname + " " + (sindex+1) + "/" + stotal);
         continue;
        }

      printf(StringSubstr(__FILE__,0,StringLen(__FILE__)-4) + "(" + EnumToString(Period()) +  ") : Processing = " + sname + " " + (sindex+1) + "/" + stotal);

      MqlTick lasttick;
      double price;
      double sell;
      double buy;
      ulong vol;
      double spread;

      // Début Traitements M1

      int handle;

      // Début Traitements M15

      price = 0;
      sell = 0;
      buy = 0;
      spread = 0;
      vol = 0;

      handle = iIchimoku(sname, Period(), tenkan_sen, kijun_sen, senkou_span_b);
      if( (handle != INVALID_HANDLE) /*&& (handleH1!=INVALID_HANDLE) && (handleH4!=INVALID_HANDLE)*/)
        {
         int max = maxhisto;
 
         int nbt = CopyBuffer(handle, TENKANSEN_LINE, 0, max, tenkan_sen_buffer);
         int nbk = CopyBuffer(handle, KIJUNSEN_LINE, 0, max, kijun_sen_buffer);
         int nbssa = CopyBuffer(handle, SENKOUSPANA_LINE, 0, max, senkou_span_a_buffer);
         int nbssb = CopyBuffer(handle, SENKOUSPANB_LINE, 0, max, senkou_span_b_buffer);
         int nbc=CopyBuffer(handle, CHIKOUSPAN_LINE, 0, max, chikou_span_buffer);

         MqlTick lasttick;
         SymbolInfoTick(sname, lasttick);
         price = lasttick.ask;
         sell = lasttick.bid; buy = lasttick.ask; spread = buy-sell; 
         ulong vol = lasttick.volume;

         //printf("buy p="+buy+ " ; ssb="+senkou_span_b_buffer[0]);

         MqlDateTime mqd;
         TimeCurrent(mqd);
         string timestamp = string(mqd.year) + "-" +  IntegerToString(mqd.mon,2,'0') + "-" + IntegerToString(mqd.day,2,'0') + " " + IntegerToString(mqd.hour,2,'0') + ":" + IntegerToString(mqd.min,2,'0') + ":" + IntegerToString(mqd.sec,2,'0') + "." + GetTickCount();
         double chikou = 0;
         if(ArraySize(chikou_span_buffer) > 26)
           {
            chikou = chikou_span_buffer[26];
           }

         if(exportPrices)
           {
            if(file_handle > 0)
              {
               FileWrite(file_handle, timestamp + ";" + sname + ";" + EnumToString(Period()) + ";" + DoubleToString(buy) + ";" + DoubleToString(sell) + ";" + DoubleToString(tenkan_sen_buffer[0]) + ";" + DoubleToString(kijun_sen_buffer[0]) + ";" + DoubleToString(chikou) + ";" + DoubleToString(senkou_span_a_buffer[0]) + ";" + DoubleToString(senkou_span_b_buffer[0]));
               //sell affiché par défaut dans MT5
              }
           }

         if(buy > senkou_span_b_buffer[0])
           {
            //printf(sname + " BUY > SSB");
           }
         if(buy < senkou_span_b_buffer[0])
           {
            //printf(sname + " BUY < SSB");
           }
         if(buy == senkou_span_b_buffer[0])
           {
            string output = /*timestamp +*/ " (" + EnumToString(Period()) + ") : " + sname + " BUY == SSB buy = " + DoubleToString(buy) + " ssb =  " + DoubleToString(senkou_span_b_buffer[0]);
            printf(output);
            SendNotification(output);
            uploadSSBAlert(timestamp, EnumToString(Period()), sname, "buy", buy, senkou_span_b_buffer[0]);
           }

         if(sell > senkou_span_b_buffer[0])
           {
            //printf(sname + " SELL > SSB");
           }
         if(sell < senkou_span_b_buffer[0])
           {
            //printf(sname + " SELL < SSB");
           }
         if(sell == senkou_span_b_buffer[0])
           {
            string output = /*timestamp +*/ " (" + EnumToString(Period()) + ") : " + sname + " SELL == SSB sell = " + DoubleToString(sell) + " ssb =  " + DoubleToString(senkou_span_b_buffer[0]);
            printf(output);
            SendNotification(output);
            uploadSSBAlert(timestamp, EnumToString(Period()), sname, "sell", sell, senkou_span_b_buffer[0]);
           }

         //NOUVEAUX TRAITEMENTS SSB/KS
         if(first_run_done[sindex] == false)
           {

            first_run_done[sindex] = true;
           }
         else
           {
            //printf("first run already done");

           }

         //printf(sname + " : M15 : OK");

        }
      else
        {
         //erreur handle
         //printf(sname + " : m1 : ERROR : " + GetLastError());
        }

      IndicatorRelease(handle);
      // Fin Traitements M15
      
      Sleep(25);

     } // fin boucle sur sindex (symbol index)
     
      int processingEnd = GetTickCount();
      printf("Processing end = " + IntegerToString(processingEnd));
      int processingDelta = processingEnd - processingStart;
      int seconds = processingDelta/1000;
      printf("Total processing time = " + IntegerToString(processingDelta) + "ms = " + IntegerToString(seconds) + "s");
      SendNotification("Total processing time = " + IntegerToString(processingDelta) + "ms = " + IntegerToString(seconds) + "s");
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
//---
   double ret=0.0;
//---

//---
   return(ret);
  }
//+------------------------------------------------------------------+
//| TesterInit function                                              |
//+------------------------------------------------------------------+
void OnTesterInit()
  {
//---

  }
//+------------------------------------------------------------------+
//| TesterPass function                                              |
//+------------------------------------------------------------------+
void OnTesterPass()
  {
//---

  }
//+------------------------------------------------------------------+
//| TesterDeinit function                                            |
//+------------------------------------------------------------------+
void OnTesterDeinit()
  {
//---

  }
//+------------------------------------------------------------------+
//| ChartEvent function                                              |
//+------------------------------------------------------------------+
void OnChartEvent(const int id,
                  const long &lparam,
                  const double &dparam,
                  const string &sparam)
  {
//---

  }
//+------------------------------------------------------------------+
//| BookEvent function                                               |
//+------------------------------------------------------------------+
void OnBookEvent(const string &symbol)
  {
//---

  }
//+------------------------------------------------------------------+

void uploadSSBAlert(string timestamp, string period, string name, string type, double price, double ssb){
   // "https://ichimoku-ea.000webhostapp.com/?notification=test"
   string cookie=NULL,headers; 
   char post[],result[]; 
   
   string ssbalert = timestamp + ";" + period + ";" + name + ";" + type + ";" + DoubleToString(price) + ";" + DoubleToString(ssb);
   
   string google_url="https://ichimoku-ea.000webhostapp.com/?upload_ssb_alert=" + ssbalert; 
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
      printf("SSB Alert sent successfully");
     } 
}


void resetAllRemoteData(){
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

void resetSSBAlertsRemoteData(){
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
