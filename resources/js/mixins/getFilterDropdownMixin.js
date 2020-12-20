import { Fund, FundList } from '@/models/Fund';
import { Company, CompanyList } from '@/models/Company';
import { UserStatus, UserStatusList } from '@/models/UserStatus';

export default {
  data() {
    return {
        funds: new FundList(),
        companies: new CompanyList(),
        userStatuses: new UserStatusList(),
    };
  },
  methods: {
      getUserStatus () {
          this.userStatuses.loading = true;
          this.userStatuses.fetch()
              .then((response) => {
                  this.userStatuses.loading = false;
                  this.userStatuses = new UserStatusList(response.data.data, response.data)
                  this.userStatuses.addItem(new UserStatus({id: 0, display_name: ''}))
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
      getCompanies () {
          this.companies.loading = true;
          this.companies.fetch()
              .then((response) => {
                  this.companies.loading = false;
                  this.companies = new CompanyList(response.data.data, response.data)
                  this.companies.addItem(new Company({id: 0, name: ''}))
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
      getFunds () {
          let that = this
          this.funds.loading = true;
          this.funds.fetch()
              .then((response) => {
                  that.funds.loading = false;
                  that.funds = new FundList(response.data.data, response.data)
                  this.funds.addItem(new Fund({id: 0, name: ''}))
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
  }
};
