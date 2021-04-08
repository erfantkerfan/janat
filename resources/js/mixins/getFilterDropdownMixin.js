import { Fund, FundList } from '@/models/Fund';
import { Loan, LoanList } from '@/models/Loan';
import { Company, CompanyList } from '@/models/Company';
import { TransactionStatus, TransactionStatusList } from '@/models/TransactionStatus';
import { UserStatus, UserStatusList } from '@/models/UserStatus';
import { LoanType, LoanTypeList } from "@/models/LoanType";
import { UserType, UserTypeList } from "@/models/UserType";

export default {
  data() {
    return {
        loans: new LoanList(),
        loanTypes: new LoanTypeList(),
        funds: new FundList(),
        companies: new CompanyList(),
        userStatuses: new UserStatusList(),
        userTypes: new UserTypeList(),
        transactionStatuses: new TransactionStatusList()
    };
  },
  methods: {
      getLoans () {
          let that = this
          this.loans.loading = true
          this.loans.fetch({
              length: 99999
          })
              .then((response) => {
                  that.loans.loading = false
                  that.loans = new LoanList(response.data.data, response.data)
                  this.loans.addItem(new Loan({id: 0, name: '-'}))
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  that.loans.loading = false
                  that.loans = new LoanList()
              })
      },
      getFunds (withEmpty) {
          let that = this
          this.funds.loading = true;
          this.funds.fetch({
              length: 99999
          })
              .then((response) => {
                  that.funds.loading = false;
                  that.funds = new FundList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.funds.addItem(new Fund({id: 0, name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  that.funds.loading = false;
                  that.funds = new FundList()
              })
      },
      getUserTypes (withEmpty) {
          this.userTypes.loading = true;
          this.userTypes.fetch({
              length: 99999
          })
              .then((response) => {
                  this.userTypes.loading = false;
                  this.userTypes = new UserTypeList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.userTypes.addItem(new UserType({id: 0, display_name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  this.userTypes.loading = false;
                  this.userTypes = new UserTypeList()
              })
      },
      getCompanies (withEmpty) {
          this.companies.loading = true;
          this.companies.fetch({
              length: 99999
          })
              .then((response) => {
                  this.companies.loading = false;
                  this.companies = new CompanyList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.companies.addItem(new Company({id: 0, name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  this.companies.loading = false;
                  this.companies = new CompanyList()
              })
      },
      getLoanTypes (withEmpty) {
          let that = this
          this.loanTypes.loading = true
          this.loanTypes.fetch({
              length: 99999
          })
              .then((response) => {
                  that.loanTypes.loading = false
                  that.loanTypes = new LoanTypeList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.loanTypes.addItem(new LoanType({id: 0, name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  that.loanTypes.loading = false
                  that.loanTypes = new LoanTypeList()
              })
      },
      getUserStatus (withEmpty) {
          this.userStatuses.loading = true;
          this.userStatuses.fetch({
              length: 99999
          })
              .then((response) => {
                  this.userStatuses.loading = false;
                  this.userStatuses = new UserStatusList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.userStatuses.addItem(new UserStatus({id: 0, display_name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  this.userStatuses.loading = false;
                  this.userStatuses = new UserStatusList()
              })
      },
      getTransactionStatus (withEmpty) {
          this.transactionStatuses.loading = true;
          this.transactionStatuses.fetch({
              length: 99999
          })
              .then((response) => {
                  this.transactionStatuses.loading = false;
                  this.transactionStatuses = new TransactionStatusList(response.data.data, response.data)
                  if (typeof withEmpty === 'undefined' || withEmpty) {
                      this.transactionStatuses.addItem(new TransactionStatus({id: 0, display_name: '-'}))
                  }
              })
              .catch((error) => {
                  this.$store.dispatch('alerts/fire', {
                      icon: 'error',
                      title: 'توجه',
                      message: 'مشکلی رخ داده است. مجدد تلاش کنید'
                  });
                  console.log('error: ', error)
                  this.transactionStatuses.loading = false;
                  this.transactionStatuses = new TransactionStatusList()
              })
      },
      customOfflineSort(value) {
          return value.sort((a, b) => {
              let sortBy = this.sortation.field
              if (sortBy.includes('.')) {
                  sortBy = sortBy.split('.')
              }

              function getObject (object, keys) {
                  if (!Array.isArray(keys)) {
                      return object[keys]
                  }
                  let newObject = Object.create(object);
                  keys.forEach((item) => {
                      newObject = newObject[item]
                  })

                  return newObject
              }

              let aSortBy = getObject(a, sortBy)
              let bSortBy = getObject(b, sortBy)
              if (this.sortation.order === 'desc') {
                  if (isNaN(aSortBy.toString().trim())) {
                      return aSortBy.toString().localeCompare(bSortBy)
                  } else {
                      return aSortBy - bSortBy
                  }
              }

              if (isNaN(aSortBy.toString().trim())) {
                  return bSortBy.toString().localeCompare(aSortBy)
              } else {
                  return bSortBy - aSortBy
              }

          })
      },
  }
};
